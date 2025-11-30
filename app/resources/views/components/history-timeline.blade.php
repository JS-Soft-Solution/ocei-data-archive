<div class="timeline">
    @foreach($histories as $history)
        <div class="timeline-item">
            <div class="timeline-badge">
                <i class="fa fa-{{ $history->action === 'status_changed' ? 'arrow-right' : 'edit' }}"></i>
            </div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h4>{{ ucfirst($history->action) }} by {{ $history->changer->name }} ({{ $history->changed_role }})</h4>
                    <small>{{ $history->created_at->format('Y-m-d H:i') }}</small>
                </div>
                <div class="timeline-body">
                    @if($history->old_values || $history->new_values)
                        <ul>
                            @foreach($history->new_values ?? [] as $key => $new)
                                <li><strong>{{ $key }}:</strong> {{ $history->old_values[$key] ?? 'N/A' }} → {{ $new }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>Record {{ $history->action }}.</p>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
