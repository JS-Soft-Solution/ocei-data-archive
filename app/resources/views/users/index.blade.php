@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <div class="card mb-3" id="customersTable" data-list='{"valueNames":["name","email","phone","role","status"],"page":10,"pagination":true}'>
        <div class="card-header">
            <div class="row flex-between-center">
                <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                    <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">User List</h5>
                </div>
                <div class="col-8 col-sm-auto ms-auto text-end ps-0">
                    <div id="table-customers-actions">
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('users.create') }}" class="btn btn-falcon-default btn-sm">
                                <span class="fas fa-plus" data-fa-transform="shrink-3 down-2"></span>
                                <span class="d-none d-sm-inline-block ms-1">New User</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">

            {{-- Filter and Search Bar --}}
            <form method="GET" action="{{ route('users.index') }}" class="row g-3 px-3 py-3 border-bottom">
                {{-- Limit Selector --}}
                <div class="col-auto">
                    <select name="limit" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="10" {{ request('limit') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('limit') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('limit') == 100 ? 'selected' : '' }}>100</option>
                        <option value="all" {{ request('limit') == 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>

                {{-- Role Filter --}}
                <div class="col-auto">
                    <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="secretary" {{ request('role') == 'secretary' ? 'selected' : '' }}>Secretary</option>
                        <option value="chairman" {{ request('role') == 'chairman' ? 'selected' : '' }}>Chairman</option>
                        <option value="office_assistant" {{ request('role') == 'office_assistant' ? 'selected' : '' }}>Office Assistant</option>
                    </select>
                </div>

                {{-- Search Input --}}
                <div class="col-auto ms-auto">
                    <div class="input-group input-group-sm">
                        <input class="form-control" type="search" name="search" placeholder="Search..." aria-label="Search" value="{{ request('search') }}" />
                        <button class="btn btn-outline-secondary" type="submit"><span class="fas fa-search"></span></button>
                    </div>
                </div>
            </form>

            <div class="table-responsive scrollbar">
                <table class="table table-sm table-striped fs-10 mb-0 overflow-hidden">
                    <thead class="bg-200">
                    <tr>
                        <th class="text-900 sort pe-1 align-middle white-space-nowrap" data-sort="name">Full Name</th>
                        <th class="text-900 sort pe-1 align-middle white-space-nowrap" data-sort="email">Email</th>
                        <th class="text-900 sort pe-1 align-middle white-space-nowrap" data-sort="phone">Mobile</th>
                        <th class="text-900 sort pe-1 align-middle white-space-nowrap" data-sort="role">Role</th>
                        <th class="text-900 sort pe-1 align-middle white-space-nowrap text-center" data-sort="status">Joined</th>
                        <th class="align-middle no-sort"></th>
                    </tr>
                    </thead>
                    <tbody class="list" id="table-customers-body">
                    @forelse($users as $user)
                        <tr class="btn-reveal-trigger">
                            <td class="name align-middle white-space-nowrap py-2">
                                <a href="{{ route('users.show', $user->id) }}" >
                                <div class="d-flex align-items-center">

                                    <div class="avatar avatar-xl me-2">
                                        @if($user->applicant_image)
                                            <img class="rounded-circle" src="{{ asset('storage/'.$user->applicant_image) }}" alt="" />
                                        @else
                                            <div class="avatar-name rounded-circle"><span>{{ substr($user->full_name, 0, 2) }}</span></div>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <h5 class="mb-0 fs--1">{{ $user->full_name }}</h5>
                                        <span class="text-500">{{ $user->user_id }}</span>
                                    </div>
                                </div>
                                </a>
                            </td>
                            <td class="email align-middle py-2"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                            <td class="phone align-middle white-space-nowrap py-2"><a href="tel:{{ $user->mobile_no }}">{{ $user->mobile_no }}</a></td>
                            <td class="role align-middle py-2">
                                <span class="badge badge-subtle-primary">{{ ucwords(str_replace('_', ' ', $user->admin_type)) }}</span>
                            </td>
                            <td class="status align-middle text-center py-2">
                                {{ $user->created_at->format('d M, Y') }}
                            </td>
                            <td class="align-middle white-space-nowrap py-2 text-end">
                                <div class="dropdown font-sans-serif position-static">
                                    <button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal" type="button" id="customer-dropdown-0" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                    <div class="dropdown-menu dropdown-menu-end border py-0" aria-labelledby="customer-dropdown-0">
                                        <div class="py-2">
                                            <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">Edit</a>
                                            <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">Details</a>
                                            @if(auth()->user()->isSuperAdmin())
                                                <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#resetPasswordModal"
                                                        data-user-id="{{ $user->id }}"
                                                        data-user-name="{{ $user->full_name }}">
                                                    Reset Password
                                                </button>
                                            @endif
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No users found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex align-items-center justify-content-center">
            {{-- Standard Laravel Pagination Links --}}
            @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
            @endif
        </div>
        <div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px">
                <div class="modal-content position-relative">
                    <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                        <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                            <h4 class="mb-1" id="modalLabel">Reset Password</h4>
                        </div>
                        <div class="p-4 pb-0">
                            <form id="adminResetPasswordForm" method="POST" action="">
                                @csrf
                                @method('PUT')

                                <p class="text-word-break fs-10">Setting new password for: <strong id="modalUserName"></strong></p>

                                <div class="mb-3">
                                    <label class="form-label" for="new_password">New Password</label>
                                    <input class="form-control" id="new_password" name="password" type="password" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="confirm_password">Confirm Password</label>
                                    <input class="form-control" id="confirm_password" name="password_confirmation" type="password" required />
                                </div>

                                <div class="d-flex justify-content-end mt-4 mb-3">
                                    <button class="btn btn-secondary btn-sm me-2" type="button" data-bs-dismiss="modal">Close</button>
                                    <button class="btn btn-primary btn-sm" type="submit">Save Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            var resetPasswordModal = document.getElementById('resetPasswordModal');
            resetPasswordModal.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                var button = event.relatedTarget;
                // Extract info from data-* attributes
                var userId = button.getAttribute('data-user-id');
                var userName = button.getAttribute('data-user-name');

                // Update the modal's content.
                var modalUserName = resetPasswordModal.querySelector('#modalUserName');
                var modalForm = resetPasswordModal.querySelector('#adminResetPasswordForm');

                modalUserName.textContent = userName;
                // Dynamically set the route action
                modalForm.action = '/users/' + userId + '/password-reset';
            });
        </script>
    @endpush
@endsection
