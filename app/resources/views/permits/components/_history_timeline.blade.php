{{-- Reusable History Timeline Component --}}
<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-history"></i> Audit Trail</h6>
    </div>
    <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
        <div class="timeline p-3">
            @foreach($histories->sortByDesc('performed_at') as $history)
            <div class="timeline-item mb-3 pb-3 border-bottom">
                <div class="d-flex align-items-start">
                    <div class="timeline-icon me-3">
                        @switch($history->action)
                            @case('created')
                                <i class="fas fa-plus-circle text-success"></i>
                                @break
                            @case('status_changed')
                                <i class="fas fa-exchange-alt text-primary"></i>
                                @break
                            @case('updated')
                                <i class="fas fa-edit text-info"></i>
                                @break
                            @case('attachment_added')
                                <i class="fas fa-paperclip text-secondary"></i>
                                @break
                            @case('attachment_deleted')
                                <i class="fas fa-trash text-warning"></i>
                                @break
                            @case('super_admin_override')
                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                @break
                            @case('soft_deleted')
                                <i class="fas fa-trash-alt text-danger"></i>
                                @break
                            @case('restored')
                                <i class="fas fa-undo text-success"></i>
                                @break
                            @default
                                <i class="fas fa-circle text-muted"></i>
                        @endswitch
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong>{{ $history->action_description }}</strong>
                            <small class="text-muted">{{ $history->performed_at->diffForHumans() }}</small>
                        </div>
                        <small class="text-muted">
                            By: <strong>{{ $history->performedBy?->name ?? 'System' }}</strong><br>
                            {{ $history->performed_at->format('d M Y, h:i A') }}
                            @if($history->ip_address)
                                <br>IP: {{ $history->ip_address }}
                            @endif
                        </small>
                        
                        @if($history->notes)
                        <div class="mt-2 p-2 bg-light rounded">
                            <small><strong>Notes:</strong> {{ $history->notes }}</small>
                        </div>
                        @endif

                        @if($history->action === 'status_changed' && $history->old_values)
                        <div class="mt-2">
                            <span class="badge bg-secondary">{{ $history->old_values['old_status'] ?? 'N/A' }}</span>
                            <i class="fas fa-arrow-right mx-2"></i>
                            <span class="badge bg-primary">{{ $history->new_values['new_status'] ?? 'N/A' }}</span>
                        </div>
                        @endif

                        @if($history->action === 'super_admin_override' && !empty($history->changed_fields))
                        <div class="mt-2 p-2 bg-warning bg-opacity-10 rounded">
                            <small><strong>⚠️ Admin Override</strong></small>
                            <ul class="mb-0 mt-1" style="font-size: 0.85em;">
                                @foreach($history->changed_fields as $field => $change)
                                <li><strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong> 
                                    {{ $change['old'] ?? 'N/A' }} → {{ $change['new'] ?? 'N/A' }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            @if($histories->isEmpty())
            <p class="text-muted text-center py-4">No history records found.</p>
            @endif
        </div>
    </div>
</div>
