<?php

namespace Modules\Schedules\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend\FlightController;
use App\Models\Bid;
use App\Models\Flight;
use App\Services\BidService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

/**
 * Extends the core controller but overrides index() only and adds addBid().
 *
 * No constructor on purpose: the inherited one is auto-injected by the container, so
 * search()/show()/bids() keep working and a change to core's constructor can't break this class.
 */
class SchedulesController extends FlightController
{
    private const PER_PAGE = 10;

    /**
     * Unique dep/arr pairs, 10 per page, each with the flights the pilot can bid on.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $pairs = $this->visibleFlights($user)
            ->select('dpt_airport_id', 'arr_airport_id')
            ->selectRaw('MIN(distance) AS pair_distance') // alias avoids the model's distance accessor
            // Optional narrowing, same parameter names the core search form uses
            ->when($request->filled('dep_icao'), fn ($q) => $q->where('dpt_airport_id', strtoupper($request->input('dep_icao'))))
            ->when($request->filled('arr_icao'), fn ($q) => $q->where('arr_airport_id', strtoupper($request->input('arr_icao'))))
            ->groupBy('dpt_airport_id', 'arr_airport_id')
            ->orderBy('dpt_airport_id')
            ->orderBy('arr_airport_id')
            ->with(['dpt_airport', 'arr_airport']) // two extra queries per page, no N+1
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // One query for every flight on this page's 10 pairs, grouped by pair for the view
        $flightsByPair = collect();
        $bidFlightIds = [];

        if ($pairs->isNotEmpty()) {
            $flights = $this->visibleFlights($user)
                ->select('id', 'airline_id', 'flight_number', 'route_code', 'route_leg', 'dpt_airport_id', 'arr_airport_id')
                ->where(function ($q) use ($pairs) {
                    foreach ($pairs as $p) {
                        $q->orWhere(fn ($w) => $w
                            ->where('dpt_airport_id', $p->dpt_airport_id)
                            ->where('arr_airport_id', $p->arr_airport_id));
                    }
                })
                ->with('airline:id,icao,iata')
                ->orderBy('flight_number')->orderBy('route_code')->orderBy('route_leg')
                ->get();

            $flightsByPair = $flights->groupBy(fn ($f) => $f->dpt_airport_id.'-'.$f->arr_airport_id);

            // Which of these flights the pilot already has a bid on
            $bidFlightIds = Bid::where('user_id', $user->id)
                ->whereIn('flight_id', $flights->pluck('id'))
                ->pluck('flight_id')
                ->flip()
                ->all();
        }

        return view('schedules::index', [
            'pairs'         => $pairs,
            'flightsByPair' => $flightsByPair,
            'bidFlightIds'  => $bidFlightIds, // keyed by flight id
            'dep_icao'      => $request->input('dep_icao'),
            'arr_icao'      => $request->input('arr_icao'),
        ]);
    }

    /**
     * Book a bid straight from the list. Goes through core's BidService, the same code
     * the API uses, so all bid settings (multiple bids, disable-on-bid, ...) are enforced.
     */
    public function addBid(Request $request): RedirectResponse
    {
        $request->validate(['flight_id' => 'required|string']);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Same visibility/restriction rules as the list, so a hand-crafted POST can't bid outside them
        $flight = $this->visibleFlights($user)->whereKey($request->input('flight_id'))->first();

        if (!$flight) {
            Flash::error('That flight is not available.');

            return redirect()->back();
        }

        try {
            app(BidService::class)->addBid($flight, $user);
            Flash::success('Flight added to your bids.');
        } catch (\Exception $e) {
            // e.g. bid limit reached, or flight already booked by someone else
            Log::info('Schedules: bid refused: '.$e->getMessage());
            Flash::error($e->getMessage() ?: 'Unable to add this bid.');
        }

        return redirect()->back();
    }

    /**
     * Flights this pilot may see/bid on, mirroring the restrictions core applies on /flights.
     * Soft-deleted flights are excluded by the model.
     */
    private function visibleFlights($user): Builder
    {
        $query = Flight::query()
            ->where(['active' => true, 'visible' => true])
            ->whereHas('airline', fn ($q) => $q->where('active', true));

        if (setting('pilots.restrict_to_company')) {
            $query->where('airline_id', $user->airline_id);
        }

        if (setting('pilots.only_show_flights_from_current')) {
            $query->where('dpt_airport_id', $user->curr_airport_id);
        }

        if (setting('pireps.restrict_aircraft_to_rank', true)
            || setting('pireps.restrict_aircraft_to_typerating', false)) {
            $subfleetIds = app(UserService::class)->getAllowableSubfleets($user)->pluck('id')->all();

            $query->where(function ($q) use ($subfleetIds) {
                $q->whereIn('id', function ($s) use ($subfleetIds) {
                    $s->select('flight_id')->from('flight_subfleet')->whereIn('subfleet_id', $subfleetIds);
                })->orWhere(function ($o) {
                    $o->whereNull('user_id')->has('subfleets');
                });
            });
        }

        return $query;
    }
}
