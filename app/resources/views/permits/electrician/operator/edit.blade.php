@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Edit Electrician Application - {{ $application->old_certificate_number }}</h4>
                        <span class="badge bg-{{ $application->status_badge_color }}"
                            style="font-size: 1rem;">{{ $application->status_label }}</span>
                    </div>

                    <div class="card-body">
                        @if(!$application->canBeEdited())
                            <div class="alert alert-warning">
                                <i class="fas fa-lock"></i> <strong>Read-Only Mode:</strong> This application cannot be edited
                                because it is currently in <strong>{{ $application->status_label }}</strong> status.
                                <a href="{{ route('ex-electrician.operator.index') }}"
                                    class="btn btn-sm btn-secondary float-end">Back to List</a>
                            </div>
                        @endif
                        {{-- Progress Bar --}}
                        <div class="mb-4">
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ request('tab', 1) * 20 }}%">
                                    Tab {{ request('tab', 1) }} of 5
                                </div>
                            </div>
                        </div>

                        {{-- Tab Navigation --}}
                        <ul class="nav nav-tabs mb-4" id="applicationTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ request('tab') == 1 || !request('tab') ? 'active' : '' }}"
                                    id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab">
                                    1. Personal Info
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ request('tab') == 2 ? 'active' : '' }}" id="tab2-tab"
                                    data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab">
                                    2. Address
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ request('tab') == 3 ? 'active' : '' }}" id="tab3-tab"
                                    data-bs-toggle="tab" data-bs-target="#tab3" type="button" role="tab">
                                    3. Education
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ request('tab') == 4 ? 'active' : '' }}" id="tab4-tab"
                                    data-bs-toggle="tab" data-bs-target="#tab4" type="button" role="tab">
                                    4. Work Experience
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ request('tab') == 5 ? 'active' : '' }}" id="tab5-tab"
                                    data-bs-toggle="tab" data-bs-target="#tab5" type="button" role="tab">
                                    5. Certificate & Attachments
                                </button>
                            </li>
                        </ul>

                        {{-- Main Form --}}
                        <form method="POST" action="{{ route('ex-electrician.operator.update', $application) }}"
                            id="applicationForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="current_tab" value="{{ request('tab', 1) }}">

                            {{-- Tab Content Container --}}
                            <div class="tab-content" id="applicationTabContent">
                                {{-- Tab 1: Personal Information --}}
                                <div class="tab-pane fade {{ request('tab') == 1 || !request('tab') ? 'show active' : '' }}"
                                    id="tab1" role="tabpanel">
                                    @include('permits.electrician.operator.tabs.tab1')
                                </div>

                                {{-- Tab 2: Address --}}
                                <div class="tab-pane fade {{ request('tab') == 2 ? 'show active' : '' }}" id="tab2"
                                    role="tabpanel">
                                    @include('permits.electrician.operator.tabs.tab2')
                                </div>

                                {{-- Tab 3: Education --}}
                                <div class="tab-pane fade {{ request('tab') == 3 ? 'show active' : '' }}" id="tab3"
                                    role="tabpanel">
                                    @include('permits.electrician.operator.tabs.tab3')
                                </div>

                                {{-- Tab 4: Work Experience --}}
                                <div class="tab-pane fade {{ request('tab') == 4 ? 'show active' : '' }}" id="tab4"
                                    role="tabpanel">
                                    @include('permits.electrician.operator.tabs.tab4')
                                </div>

                                {{-- Tab 5: Certificate & Attachments --}}
                                <div class="tab-pane fade {{ request('tab') == 5 ? 'show active' : '' }}" id="tab5"
                                    role="tabpanel">
                                    @include('permits.electrician.operator.tabs.tab5')
                                </div>
                            </div>

                            {{-- Form Actions --}}
                            @if($application->canBeEdited())
                                <div class="mt-4 mb-3 d-flex justify-content-between">
                                    <div>
                                        @if(request('tab', 1) > 1)
                                            <a href="{{ route('ex-electrician.operator.edit', ['application' => $application, 'tab' => request('tab', 1) - 1]) }}"
                                                class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Previous
                                            </a>
                                        @endif
                                    </div>

                                    <div>
                                        <button type="submit" name="action" value="save_draft" class="btn btn-info">
                                            <i class="fas fa-save"></i> Save Draft
                                        </button>

                                        @if(request('tab', 1) < 5)
                                            <button type="button" id="saveNextBtn" class="btn btn-primary">
                                                <i class="fas fa-arrow-right"></i> Save & Next
                                            </button>
                                        @else
                                            <button type="submit" name="action" value="save_draft" class="btn btn-success">
                                                <i class="fas fa-check"></i> Save All
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Submit Button (only on last tab with attachments and can be submitted) --}}
                                @if(request('tab', 1) == 5 && $application->canBeSubmitted())
                                    <div class="alert alert-info">
                                        <strong>Ready to Submit?</strong> You have completed all sections and uploaded attachments.
                                        <button type="button" class="btn btn-success ms-3" onclick="submitApplicationFromEdit()">
                                            <i class="fas fa-paper-plane"></i> Submit for Approval
                                        </button>
                                    </div>
                                @elseif(request('tab', 1) == 5 && $application->canBeEdited())
                                    <div class="alert alert-warning">
                                        <strong>Not Ready:</strong> Please upload at least one attachment before submitting.
                                    </div>
                                @endif
                            @else
                                {{-- Read-only navigation --}}
                                <div class="mt-4 mb-3 d-flex justify-content-between">
                                    <div>
                                        @if(request('tab', 1) > 1)
                                            <a href="{{ route('ex-electrician.operator.edit', ['application' => $application, 'tab' => request('tab', 1) - 1]) }}"
                                                class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Previous
                                            </a>
                                        @endif
                                    </div>
                                    <div>
                                        @if(request('tab', 1) < 5)
                                            <a href="{{ route('ex-electrician.operator.edit', ['application' => $application, 'tab' => request('tab', 1) + 1]) }}"
                                                class="btn btn-secondary">
                                                <i class="fas fa-arrow-right"></i> Next
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Tab navigation via buttons
            document.querySelectorAll('#applicationTabs button').forEach((btn, index) => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const tabNumber = index + 1;
                    window.location.href = '{{ route('ex-electrician.operator.edit', $application) }}?tab=' + tabNumber;
                });
            });

            // Save & Next button - changes form action and method
            const saveNextBtn = document.getElementById('saveNextBtn');
            if (saveNextBtn) {
                saveNextBtn.addEventListener('click', function () {
                    const form = document.getElementById('applicationForm');
                    const methodInput = form.querySelector('input[name="_method"]');

                    // Change to POST method for save-tab route
                    if (methodInput) {
                        methodInput.remove();
                    }

                    // Change action to save-tab route
                    form.action = '{{ route('ex-electrician.operator.save-tab', $application) }}';
                    form.submit();
                });
            }

            // Function to submit application for approval
            function submitApplicationFromEdit() {
                if (!confirm('Submit this application for review? You will not be able to edit it after submission.')) {
                    return;
                }

                const submitForm = document.createElement('form');
                submitForm.method = 'POST';
                submitForm.action = '{{ route('ex-electrician.operator.submit', $application) }}';

                // Get CSRF token from the main form
                const mainForm = document.getElementById('applicationForm');
                const csrfToken = mainForm.querySelector('input[name="_token"]').value;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;

                submitForm.appendChild(csrfInput);
                document.body.appendChild(submitForm);
                submitForm.submit();
            }
        </script>
    @endpush
@endsection