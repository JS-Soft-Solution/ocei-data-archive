@props([
    'action' => url()->current(),
    'showStatus' => false,
    'showDeleted' => false,
    'permitType' => 'electrician'
])

@php
    $divisions = ['Dhaka', 'Chittagong', 'Rajshahi', 'Khulna', 'Barisal', 'Sylhet', 'Rangpur', 'Mymensingh'];
    $classes = ['ক', 'খ', 'গ', 'কখ', 'খগ', 'কখগ'];
    $currentYear = (int) date('Y');
    
    // Check if any filter is active
    $hasActiveFilters = request()->hasAny(['search', 'book_number', 'class', 'year', 'district', 'division', 'status', 'date_from', 'date_to', 'show_deleted']);
@endphp

<div class="card bg-light border mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ $action }}" id="filterForm">
            <div class="row g-2 align-items-end">
                {{-- Search --}}
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-search me-1"></i>Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Certificate #, Name, NID, Mobile" value="{{ request('search') }}">
                </div>

                {{-- Book Number --}}
                <div class="col-6 col-sm-6 col-md-2">
                    <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-book me-1"></i>Book No.</label>
                    <input type="text" name="book_number" class="form-control form-control-sm"
                        placeholder="e.g. 102" value="{{ request('book_number') }}">
                </div>

                {{-- Class --}}
                <div class="col-6 col-sm-4 col-md-1">
                    <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-layer-group me-1"></i>Class</label>
                    <select name="class" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}" {{ request('class') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Year --}}
                <div class="col-6 col-sm-4 col-md-1">
                    <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-calendar-alt me-1"></i>Year</label>
                    <select name="year" class="form-select form-select-sm">
                        <option value="">All</option>
                        @for($y = $currentYear; $y >= 1980; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                {{-- District --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-map-marker-alt me-1"></i>District</label>
                    <input type="text" name="district" class="form-control form-control-sm"
                        placeholder="e.g. Dhaka, Bogura" value="{{ request('district') }}">
                </div>

                {{-- Division --}}
                <div class="col-6 col-sm-6 col-md-1">
                    <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-globe me-1"></i>Division</label>
                    <select name="division" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div }}" {{ request('division') === $div ? 'selected' : '' }}>{{ $div }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="col-12 col-sm-6 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    @if($hasActiveFilters)
                        <a href="{{ $action }}" class="btn btn-outline-secondary btn-sm" title="Clear all filters">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    @endif
                </div>
            </div>

            {{-- Secondary Row: Status / Dates / Admin options --}}
            @if($showStatus || $showDeleted || request('date_from') || request('date_to'))
                <div class="row g-2 align-items-end mt-1 pt-2 border-top">
                    @if($showStatus)
                        <div class="col-12 col-sm-6 col-md-3">
                            <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-tag me-1"></i>Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted_to_office_assistant" {{ request('status') === 'submitted_to_office_assistant' ? 'selected' : '' }}>Submitted to OA</option>
                                <option value="office_assistant_rejected" {{ request('status') === 'office_assistant_rejected' ? 'selected' : '' }}>Rejected by OA</option>
                                <option value="submitted_to_secretary" {{ request('status') === 'submitted_to_secretary' ? 'selected' : '' }}>Submitted to Secretary</option>
                                <option value="secretary_rejected" {{ request('status') === 'secretary_rejected' ? 'selected' : '' }}>Rejected by Secretary</option>
                                <option value="secretary_approved_final" {{ request('status') === 'secretary_approved_final' ? 'selected' : '' }}>Final Approved</option>
                            </select>
                        </div>
                    @endif

                    @if($showDeleted)
                        <div class="col-12 col-sm-6 col-md-2">
                            <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-trash me-1"></i>Records</label>
                            <select name="show_deleted" class="form-select form-select-sm">
                                <option value="">Active Records</option>
                                <option value="1" {{ request('show_deleted') == '1' ? 'selected' : '' }}>Deleted Only</option>
                                <option value="all" {{ request('show_deleted') == 'all' ? 'selected' : '' }}>All (incl. Deleted)</option>
                            </select>
                        </div>
                    @endif

                    <div class="col-6 col-sm-3 col-md-2">
                        <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-calendar-day me-1"></i>Date From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm"
                            value="{{ request('date_from') }}" placeholder="DD-MM-YYYY">
                    </div>

                    <div class="col-6 col-sm-3 col-md-2">
                        <label class="form-label fs-11 text-muted mb-1"><i class="fas fa-calendar-day me-1"></i>Date To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm"
                            value="{{ request('date_to') }}" placeholder="DD-MM-YYYY">
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>
