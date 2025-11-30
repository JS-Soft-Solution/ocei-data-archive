<nav class="navbar navbar-light navbar-vertical navbar-expand-xl">
    <script>
        var navbarStyle = localStorage.getItem("navbarStyle");
        if (navbarStyle && navbarStyle !== 'transparent') {
            document.querySelector('.navbar-vertical').classList.add(`navbar-${navbarStyle}`);
        }
    </script>
    <div class="d-flex align-items-center">
        <div class="toggle-icon-wrapper">
            <button class="btn navbar-toggler-humburger-icon navbar-vertical-toggle" data-bs-toggle="tooltip"
                data-bs-placement="left" title="Toggle Navigation"><span class="navbar-toggle-icon"><span
                        class="toggle-line"></span></span></button>
        </div><a class="navbar-brand" href="{{ url('/') }}">
            <div class="d-flex align-items-center py-3"><img class="me-2"
                    src="{{ asset('assets/img/icons/spot-illustrations/falcon.png') }}" alt="" width="40" /><span
                    class="font-sans-serif text-primary">falcon</span></div>
        </a>
    </div>
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <div class="navbar-vertical-content scrollbar">
            <ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">

                {{-- DASHBOARD --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}" role="button">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon"><span class="fas fa-chart-pie"></span></span>
                            <span class="nav-link-text ps-1">Dashboard</span>
                        </div>
                    </a>
                </li>

                {{-- USER MANAGEMENT SECTION --}}
                @if(auth()->check() && auth()->user()->hasRole(['super_admin']))
                    <li class="nav-item">
                        <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                            <div class="col-auto navbar-vertical-label">Administration</div>
                            <div class="col ps-0">
                                <hr class="mb-0 navbar-vertical-divider" />
                            </div>
                        </div>

                        <a class="nav-link dropdown-indicator" href="#user-management" role="button"
                            data-bs-toggle="collapse" aria-expanded="{{ request()->is('users*') ? 'true' : 'false' }}"
                            aria-controls="user-management">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span class="fas fa-users"></span></span>
                                <span class="nav-link-text ps-1">User Management</span>
                            </div>
                        </a>
                        <ul class="nav collapse {{ request()->is('users*') ? 'show' : '' }}" id="user-management">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}"
                                    href="{{ route('users.index') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text ps-1">All Users</span>
                                    </div>
                                </a>
                            </li>

                            @if(auth()->user()->isSuperAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}"
                                        href="{{ route('users.create') }}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Add New
                                                User</span></div>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{-- LEGACY PERMITS SECTION --}}
                @if(auth()->check())
                    <li class="nav-item">
                        <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                            <div class="col-auto navbar-vertical-label">Legacy Permits</div>
                            <div class="col ps-0">
                                <hr class="mb-0 navbar-vertical-divider" />
                            </div>
                        </div>
                    </li>

                    {{-- Ex-Electrician --}}
                    <li class="nav-item">
                        <a class="nav-link dropdown-indicator" href="#ex-electrician" role="button"
                            data-bs-toggle="collapse"
                            aria-expanded="{{ request()->is('ex-electrician*') ? 'true' : 'false' }}"
                            aria-controls="ex-electrician">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span class="fas fa-bolt"></span></span>
                                <span class="nav-link-text ps-1">Ex-Electrician</span>
                            </div>
                        </a>
                        <ul class="nav collapse {{ request()->is('ex-electrician*') ? 'show' : '' }}" id="ex-electrician">
                            @if(auth()->user()->role === 'data_entry_operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('ex-electrician.operator.*') ? 'active' : '' }}"
                                        href="{{ route('ex-electrician.operator.index') }}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">My
                                                Applications</span></div>
                                    </a>
                                </li>
                            @endif

                            @if(auth()->user()->role === 'office_assistant')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('ex-electrician.office-assistant.*') ? 'active' : '' }}"
                                        href="{{ route('ex-electrician.office-assistant.pending') }}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Pending
                                                Review</span></div>
                                    </a>
                                </li>
                            @endif

                            @if(auth()->user()->role === 'secretary')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('ex-electrician.secretary.*') ? 'active' : '' }}"
                                        href="{{ route('ex-electrician.secretary.pending') }}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Final
                                                Approval</span></div>
                                    </a>
                                </li>
                            @endif

                            @if(auth()->user()->role === 'chairman')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('ex-electrician.chairman.*') ? 'active' : '' }}"
                                        href="{{ route('ex-electrician.chairman.index') }}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Approved
                                                Records</span></div>
                                    </a>
                                </li>
                            @endif

                            @if(auth()->user()->role === 'super_admin')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('ex-electrician.admin.*') ? 'active' : '' }}"
                                        href="{{ route('ex-electrician.admin.index') }}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Admin
                                                Override</span></div>
                                    </a>
                                </li>
                            @endif

                            @if(in_array(auth()->user()->role, ['super_admin', 'chairman', 'secretary']))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('ex-electrician.reports.*') ? 'active' : '' }}"
                                        href="{{ route('ex-electrician.reports.index') }}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Reports</span>
                                        </div>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>

                    {{-- Ex-Supervisor --}}
                    <li class="nav-item">
                        <a class="nav-link dropdown-indicator" href="#ex-supervisor" role="button" data-bs-toggle="collapse"
                            aria-expanded="{{ request()->is('ex-supervisor*') ? 'true' : 'false' }}"
                            aria-controls="ex-supervisor">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span class="fas fa-user-tie"></span></span>
                                <span class="nav-link-text ps-1">Ex-Supervisor</span>
                            </div>
                        </a>
                        <ul class="nav collapse {{ request()->is('ex-supervisor*') ? 'show' : '' }}" id="ex-supervisor">
                            @if(auth()->user()->role === 'data_entry_operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('ex-supervisor.operator.*') ? 'active' : '' }}"
                                        href="{{ route('ex-supervisor.operator.index') }}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">My
                                                Applications</span></div>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>

                    {{-- Ex-Contractor --}}
                    <li class="nav-item">
                        <a class="nav-link dropdown-indicator" href="#ex-contractor" role="button" data-bs-toggle="collapse"
                            aria-expanded="{{ request()->is('ex-contractor*') ? 'true' : 'false' }}"
                            aria-controls="ex-contractor">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span class="fas fa-hard-hat"></span></span>
                                <span class="nav-link-text ps-1">Ex-Contractor</span>
                            </div>
                        </a>
                        <ul class="nav collapse {{ request()->is('ex-contractor*') ? 'show' : '' }}" id="ex-contractor">
                            @if(auth()->user()->role === 'data_entry_operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('ex-contractor.operator.*') ? 'active' : '' }}"
                                        href="{{ route('ex-contractor.operator.index') }}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">My
                                                Applications</span></div>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

            </ul>
        </div>
    </div>
</nav>