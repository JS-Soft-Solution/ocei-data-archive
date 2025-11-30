@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>My Drafts</h4>
            <!-- Filters -->
            <form method="GET">
                <input type="text" name="search" placeholder="Search old_certificate_number" value="{{ request('search') }}">
                <select name="status">
                    <option value="draft">Draft</option>
                    <!-- ... -->
                </select>
                <button type="submit" class="btn btn-info">Filter</button>
            </form>
        </div>
        <div class="card-body">
            @if($applications->count())
                <form method="POST" action="{{ route('ex-electrician.operator.bulk-submit') }}">
                    @csrf
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>Old Cert #</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($applications as $app)
                            <tr>
                                <td><input type="checkbox" name="selected[]" value="{{ $app->id }}"></td>
                                <td>{{ $app->old_certificate_number }}</td>
                                <td>{{ $app->applicant_name_en }}</td>
                                <td>{{ ucfirst($app->status) }}</td>
                                <td>
                                    <a href="{{ route('ex-electrician.operator.applications.edit', $app) }}" class="btn btn-sm btn-primary">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-success">Bulk Submit</button>
                </form>
                <!-- Exports -->
                <a href="{{ route('ex-electrician.reports.export-pdf') }}?{{ request()->getQueryString() }}" class="btn btn-danger">Export PDF</a>
                <a href="{{ route('ex-electrician.reports.export-excel') }}?{{ request()->getQueryString() }}" class="btn btn-success">Export Excel</a>
                {{ $applications->links() }}
            @else
                <p>No records found.</p>
            @endif
        </div>
    </div>
@endsection

<script>
    // Bulk select all
    document.getElementById('select-all').addEventListener('change', function() {
        document.querySelectorAll('input[name="selected[]"]').forEach(cb => cb.checked = this.checked);
    });
</script>
