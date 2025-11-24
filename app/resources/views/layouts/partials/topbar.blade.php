<nav class="navbar navbar-light navbar-glass navbar-top navbar-expand">
    <button class="btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse"
            aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span
                class="toggle-line"></span></span></button>
    <a class="navbar-brand me-1 me-sm-3" href="{{ url('/') }}">
        <div class="d-flex align-items-center"><img class="me-2"
                                                    src="{{ asset('assets/img/ocei-logo.png') }}" alt="" width="40" /><span
                class="font-sans-serif text-primary">OCEI</span></div>
    </a>

    {{-- Search Bar --}}
    <ul class="navbar-nav align-items-center d-none d-lg-block">
        <li class="nav-item">
            <div class="search-box" data-list='{"valueNames":["title"]}'>
                <form class="position-relative" data-bs-toggle="search" data-bs-display="static"><input
                        class="form-control search-input fuzzy-search" type="search" placeholder="Search..."
                        aria-label="Search" />
                    <span class="fas fa-search search-box-icon"></span>
                </form>
                <div class="btn-close-falcon-container position-absolute end-0 top-50 translate-middle shadow-none"
                     data-bs-dismiss="search"><button class="btn btn-link btn-close-falcon p-0"
                                                      aria-label="Close"></button></div>
                {{-- Search Dropdown Results --}}
                <div class="dropdown-menu border font-base start-0 mt-2 py-0 overflow-hidden w-100">
                    <div class="scrollbar list py-3" style="max-height: 24rem;">
                        <h6 class="dropdown-header fw-medium text-uppercase px-x1 fs-11 pt-0 pb-2">Recently Browsed
                        </h6>
                        {{-- Search items... --}}
                    </div>
                    <div class="text-center mt-n3">
                        <p class="fallback fw-bold fs-8 d-none">No Result Found.</p>
                    </div>
                </div>
            </div>
        </li>
    </ul>

    {{-- Right Side Icons (Theme Toggle, Notifications, Profile) --}}
    <ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">

        {{-- Theme Switcher --}}
        <li class="nav-item ps-2 pe-0">
            <div class="dropdown theme-control-dropdown"><a
                    class="nav-link d-flex align-items-center dropdown-toggle fa-icon-wait fs-9 pe-1 py-0"
                    href="#" role="button" id="themeSwitchDropdown" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false"><span class="fas fa-sun fs-7"
                                                                     data-fa-transform="shrink-2" data-theme-dropdown-toggle-icon="light"></span><span
                        class="fas fa-moon fs-7" data-fa-transform="shrink-3"
                        data-theme-dropdown-toggle-icon="dark"></span><span class="fas fa-adjust fs-7"
                                                                            data-fa-transform="shrink-2" data-theme-dropdown-toggle-icon="auto"></span></a>
                <div class="dropdown-menu dropdown-menu-end dropdown-caret border py-0 mt-3"
                     aria-labelledby="themeSwitchDropdown">
                    <div class="bg-white dark__bg-1000 rounded-2 py-2"><button
                            class="dropdown-item d-flex align-items-center gap-2" type="button" value="light"
                            data-theme-control="theme"><span class="fas fa-sun"></span>Light<span
                                class="fas fa-check dropdown-check-icon ms-auto text-600"></span></button>
                        <button class="dropdown-item d-flex align-items-center gap-2" type="button" value="dark"
                                data-theme-control="theme"><span class="fas fa-moon"
                                                                 data-fa-transform=""></span>Dark<span
                                class="fas fa-check dropdown-check-icon ms-auto text-600"></span></button>
                        <button class="dropdown-item d-flex align-items-center gap-2" type="button" value="auto"
                                data-theme-control="theme"><span class="fas fa-adjust"
                                                                 data-fa-transform=""></span>Auto<span
                                class="fas fa-check dropdown-check-icon ms-auto text-600"></span></button>
                    </div>
                </div>
            </div>
        </li>

        {{-- Notifications --}}
        <li class="nav-item dropdown">
            <a class="nav-link notification-indicator notification-indicator-primary px-0 fa-icon-wait"
               id="navbarDropdownNotification" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
               aria-expanded="false" data-hide-on-body-scroll="data-hide-on-body-scroll"><span class="fas fa-bell"
                                                                                               data-fa-transform="shrink-6" style="font-size: 33px;"></span></a>
            <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end dropdown-menu-card dropdown-menu-notification dropdown-caret-bg"
                 aria-labelledby="navbarDropdownNotification">
                <div class="card card-notification shadow-none">
                    <div class="card-header">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-auto">
                                <h6 class="card-header-title mb-0">Notifications</h6>
                            </div>
                            <div class="col-auto ps-0 ps-sm-3"><a class="card-link fw-normal" href="#">Mark all
                                    as read</a></div>
                        </div>
                    </div>
                    <div class="scrollbar-overlay" style="max-height:19rem">
                        <div class="list-group list-group-flush fw-normal fs-10">
                            {{-- Notification Items --}}
                            <div class="list-group-title border-bottom">NEW</div>
                            {{-- ... --}}
                        </div>
                    </div>
                    <div class="card-footer text-center border-top"><a class="card-link d-block"
                                                                       href="app/social/notifications.html">View all</a></div>
                </div>
            </div>
        </li>

        {{-- User Profile --}}
        <li class="nav-item dropdown"><a class="nav-link pe-0 ps-2" id="navbarDropdownUser" role="button"
                                         data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="avatar avatar-xl">
                    {{-- Use dynamic user avatar if available, otherwise default --}}
                    <img class="rounded-circle" src="{{ asset('storage/'.auth()->user()->applicant_image) }}" alt="User" />
                </div>
            </a>

            <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end py-0"
                 aria-labelledby="navbarDropdownUser">
                <div class="bg-white dark__bg-1000 rounded-2 py-2">
                    {{-- Optional: Display User Name Header --}}
                    <h6 class="dropdown-header fw-bold text-warning text-uppercase fs-11">
                        {{ auth()->check() ? auth()->user()->full_name : 'Account' }}
                    </h6>

                    <div class="dropdown-divider"></div>

                    {{-- Profile Link: Ensure route 'profile.show' exists or change to # --}}
                    <a class="dropdown-item" href="{{ Route::has('users.show') ? route('users.show',auth()->id()) : '#' }}">
                        Profile &amp; account
                    </a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="{{ route('password.change') }}">
                        Change Password
                    </a>

                    {{-- Logout Functionality --}}
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>

                    {{-- Hidden Logout Form to prevent CSRF --}}
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </li>
    </ul>
</nav>
<script>
    // Logic to ensure navbars are visible and layout is correct
    var navbarPosition = localStorage.getItem('navbarPosition');
    var navbarVertical = document.querySelector('.navbar-vertical');
    var navbarTopVertical = document.querySelector('.content .navbar-top');
    var navbarTop = document.querySelector('[data-layout] .navbar-top:not([data-double-top-nav');
    var navbarDoubleTop = document.querySelector('[data-double-top-nav]');
    var navbarTopCombo = document.querySelector('.content [data-navbar-top="combo"]');

    if (localStorage.getItem('navbarPosition') === 'double-top') {
        document.documentElement.classList.toggle('double-top-nav-layout');
    }

    // Ensure Sidebar is visible (if included)
    if (navbarVertical) {
        navbarVertical.removeAttribute('style');
    }

    // Ensure Topbar is visible
    if (navbarTopVertical) {
        navbarTopVertical.removeAttribute('style');
    }

    // Removed the destructive 'remove()' lines that were deleting the navbar
</script>
