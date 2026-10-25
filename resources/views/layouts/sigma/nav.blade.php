<aside id="sidebar" class="offcanvas-lg offcanvas-start flex-shrink-0 border-0" tabindex="-1" aria-labelledby="sidebarLabel" style="--bs-offcanvas-width:16rem">
    <div class="bg-dark d-flex flex-column w-100 h-100" data-bs-theme="dark">
        <div class="d-flex align-items-center gap-2 px-3 py-3">
            <a href="{{ route('frontend.home') }}"><img src="{{ public_asset('assets/sigma/img/sigma-site-header.png') }}" alt="" class="" style="height: 36px;"></a>

            <button type="button" class="btn-close ms-auto d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close menu"></button>
        </div>
        <nav class="flex-grow-1 overflow-auto px-2 py-3" aria-label="Main">
            <ul class="nav flex-column gap-1" id="mainNav">
                <li><a class="nav-link rounded-2 d-flex align-items-center gap-3 {{ request()->routeIs('frontend.dashboard.*') ? 'active' : '' }}" @if(request()->routeIs('frontend.dashboard.*')) aria-current="page" @endif href="{{ route('frontend.dashboard.index') }}"><span class="nav-icon"><i class="bi bi-grid-1x2"></i></span>Dashboard</a></li>
                <li><a class="nav-link rounded-2 d-flex align-items-center gap-3 {{ request()->is('news*') ? 'active' : '' }}" @if(request()->is('news*')) aria-current="page" @endif href="{{ url('/news') }}"><span class="nav-icon"><i class="bi bi-newspaper"></i></span>News</a></li>
                <li><a class="nav-link rounded-2 d-flex align-items-center gap-3 {{ request()->is('flightoperations*') ? 'active' : '' }}" @if(request()->is('flightoperations*')) aria-current="page" @endif href="{{ url('/flightoperations') }}"><span class="nav-icon"><i class="bi bi-signpost-split"></i></span>Flight Operations</a></li>
                <li><a class="nav-link rounded-2 d-flex align-items-center gap-3 {{ request()->routeIs('frontend.schedules.*') ? 'active' : '' }}" @if(request()->routeIs('frontend.schedules.*')) aria-current="page" @endif href="{{ url('/schedules') }}"><span class="nav-icon"><i class="bi bi-clock"></i></span>Schedules</a></li>
                <li><a class="nav-link rounded-2 d-flex align-items-center gap-3 {{ request()->routeIs('frontend.pireps.*') ? 'active' : '' }}" @if(request()->routeIs('frontend.pireps.*')) aria-current="page" @endif href="{{ route('frontend.pireps.index') }}"><span class="nav-icon"><i class="bi bi-journal-check"></i></span>My PIREPs</a></li>
                <li><a class="nav-link rounded-2 d-flex align-items-center gap-3 {{ request()->routeIs('frontend.profile.*') ? 'active' : '' }}" @if(request()->routeIs('frontend.profile.*')) aria-current="page" @endif href="{{ route('frontend.profile.index') }}"><span class="nav-icon"><i class="bi bi-person-gear"></i></span>Profile &amp; settings</a></li>
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