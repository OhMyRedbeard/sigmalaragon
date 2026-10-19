@extends('app')
@section('title', 'Schedules')

@section('content')
    {{-- Minimal markup: swap in your own styling.
         $pairs          paginator, 10 per page; each row: dpt_airport_id, arr_airport_id, pair_distance, dpt_airport, arr_airport
         $flightsByPair  Collection keyed "KJFK-KDTW" => flights (id, flight_number, route_code, route_leg, airline)
         $bidFlightIds   [flight_id => index] for flights the pilot already has a bid on --}}
    <form method="GET" action="{{ url()->current() }}">
        <input type="text" name="dep_icao" value="{{ $dep_icao }}" placeholder="Departure ICAO">
        <input type="text" name="arr_icao" value="{{ $arr_icao }}" placeholder="Arrival ICAO">
        <button type="submit">Filter</button>
    </form>

    <table>
        <thead>
        <tr>
            <th>From</th>
            <th>To</th>
            <th>Distance</th>
            <th>Book a flight</th>
        </tr>
        </thead>
        <tbody>
        @forelse($pairs as $p)
            @php
                $flights = $flightsByPair->get($p->dpt_airport_id.'-'.$p->arr_airport_id, collect());
                $open    = $flights->reject(fn ($f) => isset($bidFlightIds[$f->id]));
            @endphp
            <tr>
                <td>{{ $p->dpt_airport_id }} {{ optional($p->dpt_airport)->name }}</td>
                <td>{{ $p->arr_airport_id }} {{ optional($p->arr_airport)->name }}</td>
                <td>{{ number_format($p->pair_distance) }} nmi</td>
                <td>
                    @if($open->isNotEmpty())
                        <form method="POST" action="{{ route('frontend.schedules.bid') }}">
                            @csrf
                            <select name="flight_id">
                                @foreach($open as $f)
                                    <option value="{{ $f->id }}">
                                        {{ optional($f->airline)->icao }}{{ $f->flight_number }}{{ $f->route_code ? ' '.$f->route_code : '' }}{{ $f->route_leg ? ' L'.$f->route_leg : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit">Add to bids</button>
                        </form>
                    @else
                        <a href="{{ route('frontend.flights.bids') }}">On your bids</a>
                    @endif
                    <a href="{{ route('frontend.flights.search', ['dep_icao' => $p->dpt_airport_id, 'arr_icao' => $p->arr_airport_id]) }}">View flights</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="4">No routes found.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{ $pairs->links() }}
@endsection
