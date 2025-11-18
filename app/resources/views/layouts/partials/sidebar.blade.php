<div class="row mt-3">
    {{-- Sidebar column --}}
    <div class="col-12 col-md-3 col-xl-2 mb-3 mb-md-0">
        <div class="card">
            <div class="card-header py-2">
                <h6 class="mb-0">Navigation</h6>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('dashboard') }}"
                   class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="fas fa-tachometer-alt me-2"></span> Dashboard
                </a>

                {{-- Example admin-type-based links --}}
                @if(auth()->check() && auth()->user()->admin_type === 'super_admin')
{{--                    <a href="{{ route('users.index') }}"--}}
                    <a href=""
                       class="list-group-item list-group-item-action {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <span class="fas fa-users-cog me-2"></span> Manage Users
                    </a>
                @endif

{{--                <a href="{{ route('archives.index') }}"--}}
                <a href=""
                   class="list-group-item list-group-item-action {{ request()->routeIs('archives.*') ? 'active' : '' }}">
                    <span class="fas fa-archive me-2"></span> Data Archive
                </a>
            </div>
        </div>
    </div>

    {{-- Content column will be opened in the page view --}}
</div>
