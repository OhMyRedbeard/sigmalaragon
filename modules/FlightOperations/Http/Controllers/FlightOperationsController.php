<?php

namespace Modules\FlightOperations\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Bid;
use App\Models\Enums\AircraftStatus;
use App\Models\Flight;
use App\Models\SimBrief;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlightOperationsController extends Controller
{
    public function __construct()
    {
        // Applies to every action in this controller (previously this check lived in the
        // blade view and only protected the index page, not the POST routes).
        $this->middleware(function ($request, $next) {
            if ($request->user()->email_verified_at === null) {
                return redirect('/dashboard');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $userId = Auth::id();

        // Only the columns the view actually uses; only aircraft that can be flown.
        $aircraft = Aircraft::where('status', AircraftStatus::ACTIVE)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Airports are no longer rendered into the page (7000+ options x2). The form searches
        // via searchAirports(); we only need labels for airports re-selected after a failed submit.
        $oldAirports = Airport::whereIn('id', array_filter([old('dpt_airport_id'), old('arr_airport_id')]))
            ->get(['id', 'icao', 'name'])
            ->keyBy('id');

        // Eager load everything the table touches (was 3-4 extra queries per bid row).
        $bids = Bid::with(['flight.airline', 'flight.dpt_airport', 'flight.arr_airport'])
            ->where('user_id', $userId)
            ->orderBy('id')
            ->get();

        // One query for this user's SimBrief plans instead of one per bid.
        $simbriefs = SimBrief::where('user_id', $userId)
            ->whereNull('pirep_id')
            ->whereIn('flight_id', $bids->pluck('flight_id'))
            ->get()
            ->keyBy('flight_id');

        $simbriefEnabled = !empty(setting('simbrief.api_key'));
        $flightNumberMin = (int) config('flightoperations.flight_number_min', 1);
        $flightNumberMax = (int) config('flightoperations.flight_number_max', 5000);

        return view('flightoperations::index', compact(
            'aircraft',
            'oldAirports',
            'bids',
            'simbriefs',
            'simbriefEnabled',
            'flightNumberMin',
            'flightNumberMax'
        ));
    }

    /**
     * Type-ahead airport lookup for the create-flight form. Returns at most 20 matches.
     */
    public function searchAirports(Request $request)
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        // Escape LIKE wildcards so a user typing % or _ matches literally.
        $escaped = addcslashes($term, '%_\\');

        $airports = Airport::query()
            ->where(function ($query) use ($escaped) {
                $query->where('icao', 'like', $escaped.'%')
                    ->orWhere('iata', 'like', $escaped.'%')
                    ->orWhere('name', 'like', '%'.$escaped.'%');
            })
            // Code matches first, then name matches
            ->orderByRaw('CASE WHEN icao LIKE ? THEN 0 ELSE 1 END', [$escaped.'%'])
            ->orderBy('icao')
            ->limit(20)
            ->get(['id', 'icao', 'name']);

        return response()->json(
            $airports->map(fn ($airport) => [
                'id'    => $airport->id,
                'label' => $airport->icao.' - '.$airport->name,
            ])->values()
        );
    }

    public function createFlight(Request $request)
    {
        $min = (int) config('flightoperations.flight_number_min', 1);
        $max = (int) config('flightoperations.flight_number_max', 5000);

        $validated = $request->validate([
            'aircraft_id'    => 'required|exists:aircraft,id',
            'flight_number'  => "required|integer|between:{$min},{$max}",
            'dpt_airport_id' => 'required|exists:airports,id',
            'arr_airport_id' => 'required|exists:airports,id|different:dpt_airport_id',
        ]);

        try {
            $airline = Airline::findOrFail(config('flightoperations.airline_id', 1));
            $routeCode = config('flightoperations.route_code', 'FFM');

            $exists = Flight::where('airline_id', $airline->id)
                ->where('flight_number', $validated['flight_number'])
                ->where('route_code', $routeCode)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->with('error', $airline->icao.$validated['flight_number'].' already exists. Please pick another flight number.')
                    ->withInput();
            }

            $dptAirport = Airport::findOrFail($validated['dpt_airport_id']);
            $arrAirport = Airport::findOrFail($validated['arr_airport_id']);

            $distance = $this->calculateDistance(
                $dptAirport->lat,
                $dptAirport->lon,
                $arrAirport->lat,
                $arrAirport->lon
            );

            // Flight + bid succeed or fail together (no orphaned flights).
            $flight = DB::transaction(function () use ($airline, $routeCode, $validated, $distance) {
                $flight = Flight::create([
                    'airline_id'     => $airline->id,
                    'flight_number'  => $validated['flight_number'],
                    'route_code'     => $routeCode,
                    'dpt_airport_id' => $validated['dpt_airport_id'],
                    'arr_airport_id' => $validated['arr_airport_id'],
                    'distance'       => round($distance),
                ]);

                Bid::create([
                    'user_id'     => Auth::id(),
                    'flight_id'   => $flight->id,
                    'aircraft_id' => $validated['aircraft_id'],
                ]);

                return $flight;
            });

            return redirect()->route('flightoperations.index')
                ->with('success', $airline->icao.$flight->flight_number.' successfully created and added to your bids!');
        } catch (\Throwable $e) {
            Log::error('Error creating flight: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()
                ->with('error', 'Error creating flight. Please try again or contact an administrator.')
                ->withInput();
        }
    }

    public function deleteBid(Request $request, $bidId)
    {
        // Scoped to the current user: previously any logged-in user could delete any bid by ID.
        $bid = Bid::where('id', $bidId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        try {
            $bid->delete();
        } catch (\Throwable $e) {
            Log::error('Error deleting bid: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('error', 'Error deleting bid. Please try again.');
        }

        return redirect()->route('flightoperations.index')
            ->with('success', 'Bid successfully deleted!');
    }

    public function deleteSimbrief(Request $request, $id)
    {
        // Scoped to the current user: previously any logged-in user could delete any SimBrief plan.
        $simbrief = SimBrief::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        try {
            $simbrief->delete();
        } catch (\Throwable $e) {
            Log::error('Error deleting SimBrief: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('error', 'Error deleting SimBrief. Please try again.');
        }

        if (!$simbrief->flight_id) {
            return redirect()->route('frontend.flights.index')
                ->with('success', 'SimBrief successfully deleted!');
        }

        return redirect()->route('frontend.simbrief.generate', [
            'flight_id'   => $simbrief->flight_id,
            'aircraft_id' => $simbrief->aircraft_id,
        ])->with('success', 'SimBrief successfully deleted!');
    }

    /**
     * Great-circle distance (haversine) in nautical miles.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 3440.065;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        $a = min(1.0, $a); // guard against float drift producing NaN

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
