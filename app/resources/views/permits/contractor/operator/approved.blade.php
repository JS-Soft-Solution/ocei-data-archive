@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Approved Applications</h4>
                <span class="badge bg-success">{{ $applications->total() }} Approved</span>
            </div>
            <div class="card-body">
                {{-- Search & Filter --}}
                {{-- Search and Filter Bar --}}
                <x-permit-filter-bar />

                {{-- Applications Table --}}
        {{-- Per-Page Selector --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            @include('components.per-page-selector')
            <div>
                <span class="text-muted">Total: <strong>{{ $applications->total() }}</strong> applications</span>
            </div>
        </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Old Certificate #</th>
                                <th>Organization Name</th>
                                <th>Mobile</th>
                                <th>Class</th>
                                <th>Verified By (OA)</th>
                                <th>Approved By (Secretary)</th>
                                <th>Approval Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td>{{ $app->old_certificate_number }}</td>
                                    <td>
                                        <strong>{{ $app->company_name_en ?? $app->company_name_bn ?? 'N/A' }}</strong>
                                        @if($app->company_name_bn && $app->company_name_en)
                                            <br><small class="text-muted">{{ $app->company_name_bn }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $app->mobile_no ?? '-' }}</td>
                                    <td><span class="badge bg-secondary">{{ $app->class ?? '-' }}</span></td>
                                    <td>{{ $app->verifiedByOfficeAssistant?->full_name ?? 'N/A' }}</td>
                                    <td>{{ $app->approvedBySecretary?->full_name ?? 'N/A' }}</td>
                                    <td>{{ $app->approved_at_secretary?->format('Y-m-d') ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('ex-contractor.operator.show', $app) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No approved applications found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $applications->links() }}
            </div>
        </div>
    </div>
@endsection