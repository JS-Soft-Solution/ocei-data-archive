@extends('layouts.app')

@section('title', 'Dashboard | OCEI Data Archive')

@section('content')
    {{-- If you use the sidebar partial structure above, consider wrapping content in a row --}}
    <div class="row mt-3">
        {{-- If you keep the sidebar, change this col to col-md-9 --}}
        <div class="col-12">
            <div class="page-header mb-3">
                <h1 class="fs-3 mb-0">Dashboard</h1>
                <p class="text-700 mb-0">Welcome to the OCEI Data Archive.</p>
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="text-700">Total Records</h6>
                            <h3 class="fw-bold mb-0">{{ $totalRecords ?? 0 }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="text-700">Pending Reviews</h6>
                            <h3 class="fw-bold mb-0">{{ $pendingReviews ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                {{-- add more cards/charts as you need --}}
            </div>
        </div>
    </div>
@endsection
