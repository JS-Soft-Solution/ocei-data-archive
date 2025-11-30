@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs" id="tab-nav">
                @foreach($tabs as $num => $label)
                    <li class="nav-item">
                        <a class="nav-link {{ $currentTab == $num ? 'active' : '' }}" href="#tab{{ $num }}">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <form method="POST" action="{{ route('ex-electrician.operator.save-tab', [$record?->id ?? 'create', $currentTab]) }}" enctype="multipart/form-data">
                @csrf
                <div class="tab-content">
                    @if($currentTab == 1)
                        <!-- Tab 1 fields: name, cert#, etc. -->
                        <x-input name="applicant_name_en" :value="$record?->applicant_name_en" required />
                        <x-input name="old_certificate_number" :value="$record?->old_certificate_number" required />
                        @error('old_certificate_number') <span class="error">{{ $message }}</span> @enderror
                    @elseif($currentTab == 5)
                        <!-- Attachments -->
                        <x-file-upload :model="$record" accept="image/*,application/pdf" />
                    @endif
                    <!-- Other tabs similar -->
                </div>

                <div class="form-actions">
                    <button type="submit" name="save_draft" class="btn btn-secondary">Save Draft</button>
                    @if($currentTab < 5)
                        <button type="submit" name="save_next" class="btn btn-primary">Save & Next</button>
                    @else
                        <button type="submit" formaction="{{ route('ex-electrician.operator.applications.submit', $record) }}" class="btn btn-success" {{ !$record->isComplete() ? 'disabled' : '' }}>Submit for Approval</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
