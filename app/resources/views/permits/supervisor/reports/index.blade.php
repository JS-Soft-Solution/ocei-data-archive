@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Generate Report - Supervisor Permits</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('ex-supervisor.reports.preview') }}">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="draft">Draft</option>
                                <option value="submitted_to_office_assistant">Submitted to OA</option>
                                <option value="office_assistant_rejected">OA Rejected</option>
                                <option value="submitted_to_secretary">Submitted to Secretary</option>
                                <option value="secretary_rejected">Secretary Rejected</option>
                                <option value="secretary_approved_final">Final Approved</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">District</label>
                            <input type="text" name="district" class="form-control" placeholder="e.g., Dhaka">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Division</label>
                            <select name="division" class="form-control">
                                <option value="">All Divisions</option>
                                <option value="Dhaka">Dhaka</option>
                                <option value="Chittagong">Chittagong</option>
                                <option value="Rajshahi">Rajshahi</option>
                                <option value="Khulna">Khulna</option>
                                <option value="Barisal">Barisal</option>
                                <option value="Sylhet">Sylhet</option>
                                <option value="Rangpur">Rangpur</option>
                                <option value="Mymensingh">Mymensingh</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control"
                                placeholder="Certificate #, NID, Name, Mobile">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Preview Report
                        </button>
                        <button type="submit" formaction="{{ route('ex-supervisor.reports.export-excel') }}"
                            class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </button>
                        <button type="submit" formaction="{{ route('ex-supervisor.reports.export-pdf') }}"
                            class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Export to PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection