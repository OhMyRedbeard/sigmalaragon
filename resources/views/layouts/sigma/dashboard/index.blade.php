@extends('app')
@section('title', 'Pilot Dashboard')

@section('content')
    <!-- DASHBOARD -->
    <section id="dashboard" data-view>
        <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1">Dashboard</h1>
                <p class="text-secondary mb-0">Welcome message</p>
            </div>
            <a href="#flightops" class="btn btn-warning ms-lg-auto"><i class="bi bi-plus-lg me-2"></i>Create a
                flight</a>
        </div>
        <div class="row row-cols-2 row-cols-xl-4 g-3 mb-3">
            <div class="col">
                <div class="card border-0 shadow-sm bg-white h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-dark text-warning rounded-3 flex-shrink-0" style="width:2.75rem;height:2.75rem" data-bs-theme="dark"><i class="bi bi-clock-history fs-5"></i>
                        </div>
                        <div class="lh-sm">
                            <div class="fs-4 fw-bold font-monospace">
                                @php 
                                    $totalMinutes = DB::table('PIREPs')
                                        ->where('user_id', 2)
                                        ->sum('flight_time');

                                    $hours = floor($totalMinutes / 60);
                                    $minutes = $totalMinutes % 60;

                                    $formattedTime = sprintf('%02d:%02d', $hours, $minutes);

                                @endphp
                                {{ $formattedTime }}
                            </div>
                            <div class="small text-secondary">Flight time</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm bg-white h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-dark text-warning rounded-3 flex-shrink-0" style="width:2.75rem;height:2.75rem" data-bs-theme="dark"><i class="bi bi-airplane fs-5"></i></div>
                        <div class="lh-sm">
                            <div class="fs-4 fw-bold font-monospace">
                                @php
                                    $totalFlights = DB::table('pireps')
                                        ->where('state', '1')
                                        ->where('user_id', '2')
                                        ->count(); 
                                @endphp
                                {{ $totalFlights }}
                            </div>
                            <div class="small text-secondary">Flights logged</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm bg-white h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-dark text-warning rounded-3 flex-shrink-0" style="width:2.75rem;height:2.75rem" data-bs-theme="dark"><i class="bi bi-arrow-down-circle fs-5"></i></div>
                        <div class="lh-sm">
                            <div class="fs-4 fw-bold font-monospace">
                                @php
                                    $avgLandingRate = DB::table('pireps')
                                        ->where('user_id', '2')
                                        ->where('state', '1')
                                        ->avg('landing_rate');
                                    $averageLandingRate = $avgLandingRate = $avgLandingRate !== null ? round($avgLandingRate) : null;
                                @endphp
                                {{ $averageLandingRate }}fpm
                            </div>
                            <div class="small text-secondary">Avg landing rate</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm bg-white h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-dark text-warning rounded-3 flex-shrink-0" style="width:2.75rem;height:2.75rem" data-bs-theme="dark"><i class="bi bi-signpost-split fs-5"></i>
                        </div>
                        <div class="lh-sm">
                            <div class="fs-4 fw-bold font-monospace">
                                @php
                                    $totalDistance = DB::table('pireps')
                                        ->where('user_id', '2')
                                        ->where('state', '1')
                                        ->sum('distance');

                                    $formattedDistance = $totalDistance !== null ? number_format($totalDistance) : null;
                                @endphp
                                {{ $formattedDistance }}NM
                            </div>
                            <div class="small text-secondary">Miles flown</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm bg-white">
                    <div class="card-body pb-2 d-flex align-items-center">
                        <h2 class="h6 fw-semibold mb-0">Recent flights</h2><a href="#pireps" class="ms-auto small">View
                            all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr class="small text-secondary">
                                    <th class="ps-3 fw-medium">Flight</th>
                                    <th class="fw-medium">Route</th>
                                    <th class="fw-medium d-none d-sm-table-cell">Aircraft</th>
                                    <th class="fw-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-4">No flights yet</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm bg-white mb-3">
                    <div class="card-body">
                        <h2 class="h6 fw-semibold mb-3">Active bid</h2>
                        <p class="text-secondary small mb-0">No active bid</p>
                    </div>
                </div>
                <div class="card border-0 shadow-sm bg-white">
                    <div class="card-body">
                        <h2 class="h6 fw-semibold mb-3">Announcements</h2>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">Date</th>
                                        <th scope="col">Title</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Oct 12, 2026</td>
                                        <td>Test Title</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection