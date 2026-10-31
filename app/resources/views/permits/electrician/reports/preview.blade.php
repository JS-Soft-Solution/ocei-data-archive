@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4><i class="fas fa-chart-bar"></i> Report Preview</h4>
                <div>
                    <a href="{{ route('ex-electrician.reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Filters
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Filters Summary --}}
                <div class="alert alert-info mb-3">
                    <strong>Applied Filters:</strong>
                    @if(!empty($filters['status']))
                        Status: <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $filters['status'])) }}</span>
                    @endif
                    @if(!empty($filters['date_from']))
                        From: <span class="badge bg-secondary">{{ $filters['date_from'] }}</span>
                    @endif
                    @if(!empty($filters['date_to']))
                        To: <span class="badge bg-secondary">{{ $filters['date_to'] }}</span>
                    @endif
                    @if(!empty($filters['search']))
                        Search: <span class="badge bg-warning">{{ $filters['search'] }}</span>
                    @endif
                    @if(empty(array_filter($filters)))
                        <span class="badge bg-secondary">No filters applied</span>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Certificate #</th>
                                <th>Applicant Name</th>
                                <th>Mobile</th>
                                <th>NID</th>
                                <th>Status</th>
                                <th>Entry By</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td>{{ $app->old_certificate_number }}</td>
                                    <td>{{ $app->applicant_name_en }}</td>
                                    <td>{{ $app->mobile_no }}</td>
                                    <td>{{ $app->nid_number }}</td>
                                    <td>
                                        @if($app->status === 'secretary_approved_final')
                                            <span class="badge bg-success">Final Approved</span>
                                        @elseif($app->status === 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif(str_contains($app->status, 'rejected'))
                                            <span class="badge bg-danger">{{ $app->status_label }}</span>
                                        @else
                                            <span class="badge bg-info">{{ $app->status_label }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $app->entryBy?->full_name ?? 'N/A' }}</td>
                                    <td>{{ $app->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No applications found with the given filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $applications->links() }}

                @if($applications->count() > 0)
                    <div class="mt-3">
                        <strong>Total Records:</strong> {{ $applications->total() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection