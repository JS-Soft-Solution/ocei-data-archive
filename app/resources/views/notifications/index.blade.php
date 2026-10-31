@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">All Notifications</h5>
                </div>
                <div class="col-auto">
                    <a href="#" onclick="event.preventDefault(); markAllAsRead();" class="btn btn-sm btn-falcon-primary">
                        <i class="fas fa-check me-1"></i>Mark all as read
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    <a href="{{ $notification->url ?? '#' }}"
                       class="list-group-item list-group-item-action {{ $notification->isUnread() ? 'bg-100' : '' }}"
                       @if($notification->isUnread())
                           onclick="event.preventDefault(); markNotificationRead({{ $notification->id }}, '{{ $notification->url ?? '#' }}');"
                       @endif>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge badge-subtle-{{ $notification->type === 'application_approved' ? 'success' : ($notification->type === 'application_rejected' ? 'danger' : 'info') }} me-2">
                                        @if($notification->type === 'application_submitted')
                                            <i class="fas fa-file-upload me-1"></i> Submitted
                                        @elseif($notification->type === 'application_approved')
                                            <i class="fas fa-check me-1"></i> Approved
                                        @else
                                            <i class="fas fa-times me-1"></i> Rejected
                                        @endif
                                    </span>
                                    <small class="text-500">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <h6 class="mb-1 {{ $notification->isUnread() ? 'fw-bold' : '' }}">{{ $notification->title }}</h6>
                                <p class="mb-0 text-600">{{ $notification->message }}</p>
                                @if($notification->data && isset($notification->data['certificate_number']))
                                    <small class="text-500">Certificate: {{ $notification->data['certificate_number'] }}</small>
                                @endif
                            </div>
                            @if($notification->isUnread())
                                <span class="badge bg-primary rounded-pill ms-2">New</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="text-center py-5 text-500">
                        <i class="fas fa-bell-slash fs-3 mb-3"></i>
                        <p class="mb-0">No notifications yet</p>
                    </div>
                @endforelse
            </div>
        </div>
        @if($notifications->hasPages())
            <div class="card-footer">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection
