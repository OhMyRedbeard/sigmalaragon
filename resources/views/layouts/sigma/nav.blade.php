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
                <li><a class="nav-link rounded-2 d-flex align-items-center gap-3" href="{{ route('frontend.dashboard.index')}}"><i class="bi bi-grid-1x2"></i>Dashboard</a></li>
                <li><a class="nav-link rounded-2 d-flex align-items-center gap-3" href="{{ url('/news') }}"><i class="bi bi-newspaper"></i>News</a></li>
                <li><a class="nav-link rounded-2 d-flex align-items-center gap-3" href="{{ url('/flightoperations')}}"><i class="bi bi-signpost-split"></i>Flight Operations</a></li>
                <li><a class="nav-link rounded-2 d-flex align-items-center gap-3" href="{{ route('frontend.pireps.index') }}"><i class="bi bi-journal-check"></i>My PIREPs</a></li>
                {{--<li><a class="nav-link rounded-2 d-flex align-items-center gap-3" href="?page=fleet"><i class="bi bi-airplane-engines"></i>Fleet</a></li> --}}
                <li><a class="nav-link rounded-2 d-flex align-items-center gap-3" href="{{ route('frontend.profile.index') }}"><i class="bi bi-person-gear"></i>Profile &amp; settings</a></li>
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