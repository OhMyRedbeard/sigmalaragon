<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <title>@yield('title') - {{ config('app.name') }}</title>


    {{-- Start of required lines block. DON'T REMOVE THESE LINES! They're required or might break things --}}
    <meta name="base-url" content="{!! url('') !!}">
    <meta name="api-key" content="{!! Auth::check() ? Auth::user()->api_key : '' !!}">
    <meta name="csrf-token" content="{!! csrf_token() !!}">
    {{-- End the required lines block --}}
    <link rel="stylesheet" href="{{ public_asset('/assets/sigma/css/sigma.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    {{-- Start of the required files in the head block --}}
    {{--
    <link href="{{ public_mix('/assets/global/css/vendor.css') }}" rel="stylesheet" /> --}}

    @yield('scripts_head')
    {{-- End of the required stuff in the head block --}}
</head>

<body class="vh-100 overflow-hidden">
    <div class="d-flex h-100">
        @if(Auth::check())
            <aside id="sidebar" class="offcanvas-lg offcanvas-start flex-shrink-0 border-0" tabindex="-1" aria-labelledby="sidebarLabel" style="--bs-offcanvas-width:16rem">
                <div class="bg-dark d-flex flex-column w-100 h-100" data-bs-theme="dark">
                    <div class="d-flex align-items-center gap-2 px-3 py-3 border-bottom border-secondary-subtle">
                        <img src="./img/sigma-logo.png" alt="" style="max-height:36px">
                        <div class="lh-sm" id="sidebarLabel">
                            <div class="fw-bold font-monospace" style="color: var(--bs-sigma-white);">SIGMA</div>
                            <div class="small text-secondary">Crew Center</div>
                        </div>
                        <button type="button" class="btn-close ms-auto d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close menu"></button>
                    </div>
                    <nav class="flex-grow-1 overflow-auto px-2 py-3" aria-label="Main">
                        <ul class="nav flex-column gap-1" id="mainNav">
                            <li><a class="nav-link rounded-2 d-flex align-items-center gap-3" href="?page=dashboard"><i class="bi bi-grid-1x2"></i>Dashboard</a></li>
                            <li><a class="nav-link rounded-2 d-flex align-items-center gap-3" href="?page=flightops"><i class="bi bi-signpost-split"></i>Flight Operations</a></li>
                            <li><a class="nav-link rounded-2 d-flex align-items-center gap-3" href="?page=pireps"><i class="bi bi-journal-check"></i>My PIREPs</a></li>
                            <li><a class="nav-link rounded-2 d-flex align-items-center gap-3" href="?page=fleet"><i class="bi bi-airplane-engines"></i>Fleet</a></li>
                            <li><a class="nav-link rounded-2 d-flex align-items-center gap-3" href="?page=profile"><i class="bi bi-person-gear"></i>Profile &amp; settings</a></li>
                        </ul>
                    </nav>
                    <div class="p-3 border-top border-secondary-subtle">
                        <div class="d-flex align-items-center gap-3 mb-2"><i class="bi bi-broadcast text-warning fs-5"></i>
                            <div class="lh-sm">
                                <div class="small fw-semibold">vmsACARS</div>
                                <div class="small text-secondary">Status</div>
                            </div>
                        </div>
                        <a href="https://flysigmava.us" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-box-arrow-up-right me-2"></i>Sigma website</a>
                    </div>
                </div>
            </aside>
        @else
            NOT LOGGED IN
        @endif
        <div class="flex-grow-1 d-flex flex-column h-100 overflow-hidden">

            <header class="bg-dark d-flex align-items-center gap-3 px-3 py-2 flex-shrink-0" data-bs-theme="dark">
                <button class="btn btn-outline-secondary border-0 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Open menu"><i class="bi bi-list fs-4"></i></button>
                <form class="d-none d-sm-block flex-grow-1" style="max-width:26rem" role="search">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white bg-opacity-10 border-0 text-secondary"><i class="bi bi-search"></i></span>
                        <input type="search" class="form-control bg-white bg-opacity-10 border-0 text-white" placeholder="Search airports, routes, pilots" aria-label="Search">
                    </div>
                </form>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <span class="d-none d-md-inline font-monospace small text-secondary me-2" id="zulu">--:--Z</span>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications"><i class="bi bi-bell"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:16rem">
                            <li class="dropdown-item-text small text-secondary">Notifications</li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary border-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center small" style="width:2rem;height:2rem"><i class="bi bi-person-fill"></i></span>
                            <span class="d-none d-md-inline small text-start lh-sm">Pilot name<br><span class="font-monospace text-secondary">Pilot ID</span></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="#profile"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Sign
                                    out</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="flex-grow-1 overflow-auto p-3 p-lg-4">
                <footer class="small text-secondary pt-4">Sigma Virtual Airlines is a virtual airline operating for flight
                    simulation purposes only and is not affiliated with any real-world airline, alliance, or aircraft
                    manufacturer. No actual flights take place. &copy; <span id="yr"></span></footer>
            </main>
        </div>
    </div>

    </div>
</body>

</html>