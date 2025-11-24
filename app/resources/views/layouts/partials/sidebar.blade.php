{{--<nav class="navbar navbar-light navbar-vertical navbar-expand-xl" style="display: none;">--}}
{{--    <script>--}}
{{--        var navbarStyle = localStorage.getItem("navbarStyle");--}}
{{--        if (navbarStyle && navbarStyle !== 'transparent') {--}}
{{--            document.querySelector('.navbar-vertical').classList.add(`navbar-${navbarStyle}`);--}}
{{--        }--}}
{{--    </script>--}}
{{--    <div class="d-flex align-items-center">--}}
{{--        <div class="toggle-icon-wrapper">--}}
{{--            <button class="btn navbar-toggler-humburger-icon navbar-vertical-toggle" data-bs-toggle="tooltip"--}}
{{--                    data-bs-placement="left" title="Toggle Navigation"><span class="navbar-toggle-icon"><span--}}
{{--                        class="toggle-line"></span></span></button>--}}
{{--        </div><a class="navbar-brand" href="{{ url('/') }}">--}}
{{--            <div class="d-flex align-items-center py-3"><img class="me-2"--}}
{{--                                                             src="{{ asset('assets/img/icons/spot-illustrations/falcon.png') }}" alt="" width="40" /><span--}}
{{--                    class="font-sans-serif text-primary">falcon</span></div>--}}
{{--        </a>--}}
{{--    </div>--}}
{{--    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">--}}
{{--        <div class="navbar-vertical-content scrollbar">--}}
{{--            <ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">--}}

{{--                --}}{{-- DASHBOARD --}}
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link" href="{{ route('dashboard') }}" role="button">--}}
{{--                        <div class="d-flex align-items-center">--}}
{{--                            <span class="nav-link-icon"><span class="fas fa-chart-pie"></span></span>--}}
{{--                            <span class="nav-link-text ps-1">Dashboard</span>--}}
{{--                        </div>--}}
{{--                    </a>--}}
{{--                </li>--}}

{{--                --}}{{-- USER MANAGEMENT SECTION --}}
{{--                --}}{{-- Only visible to specific roles --}}
{{--                @if(auth()->check() && auth()->user()->hasRole(['super_admin']))--}}
{{--                    <li class="nav-item">--}}
{{--                        <div class="row navbar-vertical-label-wrapper mt-3 mb-2">--}}
{{--                            <div class="col-auto navbar-vertical-label">Administration</div>--}}
{{--                            <div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div>--}}
{{--                        </div>--}}

{{--                        <a class="nav-link dropdown-indicator" href="#user-management" role="button" data-bs-toggle="collapse"--}}
{{--                           aria-expanded="{{ request()->is('users*') ? 'true' : 'false' }}" aria-controls="user-management">--}}
{{--                            <div class="d-flex align-items-center">--}}
{{--                                <span class="nav-link-icon"><span class="fas fa-users"></span></span>--}}
{{--                                <span class="nav-link-text ps-1">User Management</span>--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                        <ul class="nav collapse {{ request()->is('users*') ? 'show' : '' }}" id="user-management">--}}

{{--                            --}}{{-- View User List --}}
{{--                            <li class="nav-item">--}}
{{--                                <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">--}}
{{--                                    <div class="d-flex align-items-center"><span class="nav-link-text ps-1">All Users</span></div>--}}
{{--                                </a>--}}
{{--                            </li>--}}

{{--                            --}}{{-- Create New User (Restricted to Super Admin) --}}
{{--                            @if(auth()->user()->isSuperAdmin())--}}
{{--                                <li class="nav-item">--}}
{{--                                    <a class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}" href="{{ route('users.create') }}">--}}
{{--                                        <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Add New User</span></div>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                            @endif--}}
{{--                        </ul>--}}
{{--                    </li>--}}
{{--                @endif--}}

{{--                --}}{{-- DATA ENTRY SECTION (Example for other roles) --}}
{{--                @if(auth()->check() && auth()->user()->hasRole(['super_admin', 'data_entry_operator','office_assistant']))--}}
{{--                    <li class="nav-item">--}}
{{--                        <div class="row navbar-vertical-label-wrapper mt-3 mb-2">--}}
{{--                            <div class="col-auto navbar-vertical-label">Operations</div>--}}
{{--                            <div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div>--}}
{{--                        </div>--}}
{{--                        <a class="nav-link" href="#" role="button">--}}
{{--                            <div class="d-flex align-items-center">--}}
{{--                                <span class="nav-link-icon"><span class="fas fa-database"></span></span>--}}
{{--                                <span class="nav-link-text ps-1">Data Entry</span>--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                @endif--}}

{{--            </ul>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</nav>--}}

<nav class="navbar navbar-light navbar-vertical navbar-expand-xl" style="display: none;">
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

                {{-- DASHBOARD (Everyone) --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}" role="button">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon"><span class="fas fa-chart-pie"></span></span>
                            <span class="nav-link-text ps-1">Dashboard</span>
                        </div>
                    </a>
                </li>

                {{-- ============================================================== --}}
                {{-- SUPER ADMIN: CONSOLIDATED MENU (One Section Per Permit Type) --}}
                {{-- ============================================================== --}}
                @if(auth()->user()->isSuperAdmin())
                    <li class="nav-item">
                        <div class="row navbar-vertical-label-wrapper mt-3 mb-2"><div class="col-auto navbar-vertical-label">Master Control</div><div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div></div>

                        {{-- 1. ELECTRICIAN MASTER --}}
                        <a class="nav-link dropdown-indicator" href="#sa-electrician" role="button" data-bs-toggle="collapse" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-bolt"></span></span><span class="nav-link-text ps-1">Electrician Permits</span></div>
                        </a>
                        <ul class="nav collapse" id="sa-electrician">
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.electrician.search') }}">New / Search</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.electrician.drafts') }}">Drafts</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.electrician.pending') }}">Pending Verification</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.electrician.secretary.pending') }}">Pending Approval</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.electrician.secretary.approved') }}">Approved List</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.electrician.rejected') }}">Rejected List</a></li>
                        </ul>

                        {{-- 2. SUPERVISOR MASTER --}}
                        <a class="nav-link dropdown-indicator" href="#sa-supervisor" role="button" data-bs-toggle="collapse" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-user-tie"></span></span><span class="nav-link-text ps-1">Supervisor Permits</span></div>
                        </a>
                        <ul class="nav collapse" id="sa-supervisor">
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.supervisor.search') }}">New / Search</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.supervisor.drafts') }}">Drafts</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.supervisor.pending') }}">Pending Verification</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.supervisor.secretary.pending') }}">Pending Approval</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.supervisor.secretary.approved') }}">Approved List</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.supervisor.rejected') }}">Rejected List</a></li>
                        </ul>

                        {{-- 3. CONTRACTOR MASTER --}}
                        <a class="nav-link dropdown-indicator" href="#sa-contractor" role="button" data-bs-toggle="collapse" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-building"></span></span><span class="nav-link-text ps-1">Contractor Permits</span></div>
                        </a>
                        <ul class="nav collapse" id="sa-contractor">
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.contractor.search') }}">New / Search</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.contractor.drafts') }}">Drafts</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.contractor.pending') }}">Pending Verification</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.contractor.secretary.pending') }}">Pending Approval</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.contractor.secretary.approved') }}">Approved List</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('permits.contractor.rejected') }}">Rejected List</a></li>
                        </ul>

                        {{-- USER MANAGEMENT --}}
                        <div class="row navbar-vertical-label-wrapper mt-3 mb-2"><div class="col-auto navbar-vertical-label">System</div><div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div></div>
                        <a class="nav-link" href="{{ route('users.index') }}">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-users"></span></span><span class="nav-link-text ps-1">User Management</span></div>
                        </a>
                    </li>

                    {{-- ============================================================== --}}
                    {{-- REGULAR USERS: ROLE SPECIFIC MENUS (Hidden from Super Admin) --}}
                    {{-- ============================================================== --}}
                @else

                    {{-- DATA ENTRY OPERATOR --}}
                    @if(auth()->user()->hasRole('data_entry_operator'))
                        <li class="nav-item">
                            <div class="row navbar-vertical-label-wrapper mt-3 mb-2"><div class="col-auto navbar-vertical-label">Entry Operations</div><div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div></div>

                            {{-- Electrician --}}
                            <a class="nav-link dropdown-indicator" href="#op-electrician" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-bolt"></span></span><span class="nav-link-text ps-1">Electrician</span></div>
                            </a>
                            <ul class="nav collapse" id="op-electrician">
                                <li class="nav-item"><a class="nav-link" href="{{ route('permits.electrician.search') }}">New / Search</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('permits.electrician.drafts') }}">My Drafts</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('permits.electrician.rejected') }}">Returned</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('permits.electrician.submitted') }}">Submitted</a></li>
                            </ul>

                            {{-- Supervisor --}}
                            <a class="nav-link dropdown-indicator" href="#op-supervisor" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-user-tie"></span></span><span class="nav-link-text ps-1">Supervisor</span></div>
                            </a>
                            <ul class="nav collapse" id="op-supervisor">
                                <li class="nav-item"><a class="nav-link" href="{{ route('permits.supervisor.search') }}">New / Search</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('permits.supervisor.drafts') }}">My Drafts</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('permits.supervisor.rejected') }}">Returned</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('permits.supervisor.submitted') }}">Submitted</a></li>
                            </ul>

                            {{-- Contractor --}}
                            <a class="nav-link dropdown-indicator" href="#op-contractor" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-building"></span></span><span class="nav-link-text ps-1">Contractor</span></div>
                            </a>
                            <ul class="nav collapse" id="op-contractor">
                                <li class="nav-item"><a class="nav-link" href="{{ route('permits.contractor.search') }}">New / Search</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('permits.contractor.drafts') }}">My Drafts</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('permits.contractor.rejected') }}">Returned</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('permits.contractor.submitted') }}">Submitted</a></li>
                            </ul>
                        </li>
                    @endif

                    {{-- OFFICE ASSISTANT --}}
                    @if(auth()->user()->hasRole('office_assistant'))
                        <li class="nav-item">
                            <div class="row navbar-vertical-label-wrapper mt-3 mb-2"><div class="col-auto navbar-vertical-label">Verification</div><div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div></div>

                            <a class="nav-link" href="{{ route('permits.electrician.pending') }}">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-bolt"></span></span><span class="nav-link-text ps-1">Electrician (Pending)</span></div>
                            </a>
                            <a class="nav-link" href="{{ route('permits.supervisor.pending') }}">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-user-tie"></span></span><span class="nav-link-text ps-1">Supervisor (Pending)</span></div>
                            </a>
                            <a class="nav-link" href="{{ route('permits.contractor.pending') }}">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-building"></span></span><span class="nav-link-text ps-1">Contractor (Pending)</span></div>
                            </a>
                        </li>
                    @endif

                    {{-- SECRETARY --}}
                    @if(auth()->user()->hasRole('secretary'))
                        <li class="nav-item">
                            <div class="row navbar-vertical-label-wrapper mt-3 mb-2"><div class="col-auto navbar-vertical-label">Final Approval</div><div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div></div>

                            <a class="nav-link" href="{{ route('permits.electrician.secretary.pending') }}">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-bolt"></span></span><span class="nav-link-text ps-1">Electrician</span></div>
                            </a>
                            <a class="nav-link" href="{{ route('permits.supervisor.secretary.pending') }}">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-user-tie"></span></span><span class="nav-link-text ps-1">Supervisor</span></div>
                            </a>
                            <a class="nav-link" href="{{ route('permits.contractor.secretary.pending') }}">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-building"></span></span><span class="nav-link-text ps-1">Contractor</span></div>
                            </a>
                        </li>
                    @endif

                    {{-- CHAIRMAN --}}
                    @if(auth()->user()->hasRole('chairman'))
                        <li class="nav-item">
                            <div class="row navbar-vertical-label-wrapper mt-3 mb-2"><div class="col-auto navbar-vertical-label">Overview</div><div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div></div>
                            <a class="nav-link" href="{{ route('permits.electrician.secretary.approved') }}">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-list"></span></span><span class="nav-link-text ps-1">All Approved Lists</span></div>
                            </a>
                        </li>
                    @endif
                @endif

            </ul>
        </div>
    </div>
</nav>
