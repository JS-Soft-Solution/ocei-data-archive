@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">
    {{-- Header Banner --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 text-primary">
                <i class="fas fa-user-tie text-info me-2"></i>Supervisor Archive – Quick Data Entry
            </h4>
            <span class="text-muted small">
                Transcribe handwritten supervisor certificate register page details into the archive with real-time scan inspection.
            </span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ex-supervisor.operator.create') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-columns me-1"></i> Switch to 5-Tab Form
            </a>
            <a href="{{ route('ex-supervisor.operator.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Applications List
            </a>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Main Split-Screen Form --}}
    <form method="POST" action="{{ route('ex-supervisor.operator.quick-store') }}" enctype="multipart/form-data" id="quickEntryForm">
        @csrf
        <input type="hidden" name="form_action" id="formActionInput" value="save_draft">

        <div class="row g-3">
            {{-- Left Side: Interactive Document / Scan Viewer --}}
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                        <span class="fw-bold text-dark">
                            <i class="fas fa-file-image me-1 text-primary"></i> Register Page Scan <span class="text-danger">*</span>
                        </span>
                        <div class="btn-group btn-group-sm" id="viewerToolbar" style="display: none;">
                            <button type="button" class="btn btn-outline-secondary" id="btnZoomIn" title="Zoom In">
                                <i class="fas fa-search-plus"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btnZoomOut" title="Zoom Out">
                                <i class="fas fa-search-minus"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btnRotate" title="Rotate 90°">
                                <i class="fas fa-redo"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btnReset" title="Reset View">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-3 d-flex flex-column">
                        {{-- File Input --}}
                        <div class="mb-2">
                            <input type="file" name="scanned_document" id="scannedDocumentInput"
                                class="form-control form-control-sm @error('scanned_document') is-invalid @enderror"
                                accept=".jpg,.jpeg,.png,.pdf,.webp" required>
                            @error('scanned_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">Select the scanned physical register page (JPG, PNG or PDF, max 10MB).</div>
                        </div>

                        {{-- Scan Preview Canvas Container --}}
                        <div id="previewContainer" class="border rounded bg-dark flex-grow-1 position-relative overflow-hidden d-flex align-items-center justify-content-center"
                            style="min-height: 520px; max-height: 720px; cursor: grab;">
                            
                            {{-- Placeholder when no image selected --}}
                            <div id="uploadPlaceholder" class="text-center text-secondary p-4">
                                <i class="fas fa-cloud-upload-alt fa-3x mb-2 text-muted"></i>
                                <p class="mb-1 fw-bold">Select a scanned supervisor register file above</p>
                                <span class="small text-muted">The document will render here with zoom, rotate, and pan tools for manual data transcription.</span>
                            </div>

                            {{-- Image Display Element --}}
                            <img id="scannedImagePreview" src="" alt="Supervisor Register Scan" 
                                style="display: none; max-width: 100%; transition: transform 0.1s ease-out; user-select: none;" draggable="false">

                            {{-- PDF Preview Embed Element --}}
                            <iframe id="pdfPreviewFrame" src="" style="display: none; width: 100%; height: 100%; min-height: 520px;" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: Minimal Form Fields (Directly from Register Page) --}}
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-light py-2">
                        <span class="fw-bold text-dark">
                            <i class="fas fa-keyboard me-1 text-success"></i> Register Record Details (Manual Input)
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2">
                            {{-- Book Number & Old Registration Number --}}
                            <div class="col-md-5">
                                <label for="book_number" class="form-label small fw-bold mb-1">
                                    Book / Page No. (বই নম্বর / পৃষ্ঠা নং)
                                </label>
                                <input type="text" class="form-control form-control-sm @error('book_number') is-invalid @enderror"
                                    id="book_number" name="book_number" value="{{ old('book_number') }}"
                                    placeholder="e.g. ৯৩২৫, ১২২৭৫" autofocus>
                                @error('book_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-7">
                                <label for="old_certificate_number" class="form-label small fw-bold mb-1">
                                    Registration No. (নিবন্ধন নং এস) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm @error('old_certificate_number') is-invalid @enderror"
                                    id="old_certificate_number" name="old_certificate_number" value="{{ old('old_certificate_number') }}"
                                    placeholder="e.g. ৩৭/৯৩২৫, ৫০/১২২৭৫" required>
                                @error('old_certificate_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Approved Class with Quick-Select Buttons --}}
                            <div class="col-12 mt-2">
                                <label for="class" class="form-label small fw-bold mb-1">
                                    Certificate Class (সার্টিফিকেট শ্রেণী)
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control form-control-sm @error('class') is-invalid @enderror"
                                        id="class" name="class" value="{{ old('class', 'কখগ') }}" placeholder="e.g. কখগ, খগ, ক">
                                    <button class="btn btn-outline-secondary" type="button" onclick="setClass('কখগ')">কখগ (ABC)</button>
                                    <button class="btn btn-outline-secondary" type="button" onclick="setClass('খগ')">খগ (BC)</button>
                                    <button class="btn btn-outline-secondary" type="button" onclick="setClass('ক')">ক (A)</button>
                                    <button class="btn btn-outline-secondary" type="button" onclick="setClass('খ')">খ (B)</button>
                                    <button class="btn btn-outline-secondary" type="button" onclick="setClass('গ')">গ (C)</button>
                                </div>
                                @error('class')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Applicant Name (Bangla & English) --}}
                            <div class="col-md-6 mt-2">
                                <label for="applicant_name_bn" class="form-label small fw-bold mb-1">
                                    Name (নাম - বাংলা)
                                </label>
                                <input type="text" class="form-control form-control-sm @error('applicant_name_bn') is-invalid @enderror"
                                    id="applicant_name_bn" name="applicant_name_bn" value="{{ old('applicant_name_bn') }}"
                                    placeholder="e.g. মোঃ আব্দুল করিম মিয়া">
                                @error('applicant_name_bn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-2">
                                <label for="applicant_name_en" class="form-label small fw-bold mb-1">
                                    Name (English)
                                </label>
                                <input type="text" class="form-control form-control-sm @error('applicant_name_en') is-invalid @enderror"
                                    id="applicant_name_en" name="applicant_name_en" value="{{ old('applicant_name_en') }}"
                                    placeholder="e.g. Md. Abdul Karim Mia">
                                @error('applicant_name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Father's Name --}}
                            <div class="col-12 mt-2">
                                <label for="father_name" class="form-label small fw-bold mb-1">
                                    Father's Name (পিতার নাম)
                                </label>
                                <input type="text" class="form-control form-control-sm @error('father_name') is-invalid @enderror"
                                    id="father_name" name="father_name" value="{{ old('father_name') }}"
                                    placeholder="e.g. মৃত- আব্দুল মান্নান মিয়া">
                                @error('father_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Address (One Block Text) --}}
                            <div class="col-12 mt-2">
                                <label for="village" class="form-label small fw-bold mb-1">
                                    Address (ঠিকানা – গ্রাম/রোড, ডাকঘর, থানা, জেলা)
                                </label>
                                <textarea class="form-control form-control-sm @error('village') is-invalid @enderror"
                                    id="village" name="village" rows="2"
                                    placeholder="e.g. গ্রাম+পোঃ বাহাদুরপুর, থানা- শিবচর, জেলা- মাদারীপুর">{{ old('village') }}</textarea>
                                @error('village')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Qualification & Passing Year --}}
                            <div class="col-md-7 mt-2">
                                <label for="degree" class="form-label small fw-bold mb-1">
                                    Qualification (শিক্ষাগত যোগ্যতা)
                                </label>
                                <input type="text" class="form-control form-control-sm @error('degree') is-invalid @enderror"
                                    id="degree" name="degree" value="{{ old('degree') }}"
                                    placeholder="e.g. প্রকৌশল ডিপ্লোমা (পাওয়ার), বি এস সি ইঞ্জিনিয়ারিং">
                                @error('degree')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-5 mt-2">
                                <label for="passing_year" class="form-label small fw-bold mb-1">
                                    Passing Year (পাসের সন)
                                </label>
                                <input type="text" class="form-control form-control-sm @error('passing_year') is-invalid @enderror"
                                    id="passing_year" name="passing_year" value="{{ old('passing_year') }}"
                                    placeholder="e.g. ১৯৯৪ ইং, সিভিল - ২০০৯">
                                @error('passing_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Issue / Passing Date & Latest Expiry Date --}}
                            <div class="col-md-6 mt-2">
                                <label for="issue_date" class="form-label small fw-bold mb-1">
                                    Passing Date (পাসের তারিখ)
                                </label>
                                <input type="date" class="form-control form-control-sm @error('issue_date') is-invalid @enderror"
                                    id="issue_date" name="issue_date" value="{{ old('issue_date') }}">
                                @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-2">
                                <label for="expiry_date" class="form-label small fw-bold mb-1">
                                    Latest Expiry Date (মেয়াদ শেষ হওয়ার তারিখ)
                                </label>
                                <input type="date" class="form-control form-control-sm @error('expiry_date') is-invalid @enderror"
                                    id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}">
                                @error('expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Optional Details: Mobile & NID --}}
                            <div class="col-md-6 mt-2">
                                <label for="mobile_no" class="form-label small fw-bold mb-1">
                                    Mobile No. (মোবাইল নম্বর - optional)
                                </label>
                                <input type="text" class="form-control form-control-sm @error('mobile_no') is-invalid @enderror"
                                    id="mobile_no" name="mobile_no" value="{{ old('mobile_no') }}"
                                    placeholder="e.g. 01700000000">
                                @error('mobile_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-2">
                                <label for="nid_number" class="form-label small fw-bold mb-1">
                                    NID No. (জাতীয় পরিচয়পত্র - optional)
                                </label>
                                <input type="text" class="form-control form-control-sm @error('nid_number') is-invalid @enderror"
                                    id="nid_number" name="nid_number" value="{{ old('nid_number') }}"
                                    placeholder="e.g. 1980000000000">
                                @error('nid_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                <a href="{{ route('ex-supervisor.operator.index') }}" class="btn btn-outline-secondary btn-sm">
                                    Cancel
                                </a>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="submitForm('save_draft', false)">
                                    <i class="fas fa-save me-1"></i> Save Draft
                                </button>
                                <button type="button" class="btn btn-info btn-sm text-white" onclick="submitForm('save_draft', true)">
                                    <i class="fas fa-step-forward me-1"></i> Draft & Next
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="submitForm('submit_for_approval', false)">
                                    <i class="fas fa-paper-plane me-1"></i> Submit for Review
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" onclick="submitForm('submit_for_approval', true)">
                                    <i class="fas fa-check-double me-1"></i> Submit & Next Record
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function setClass(val) {
        document.getElementById('class').value = val;
    }

    function submitForm(action, isNext) {
        document.getElementById('formActionInput').value = action;
        if (isNext) {
            let nextInput = document.createElement('input');
            nextInput.type = 'hidden';
            nextInput.name = 'save_and_next';
            nextInput.value = '1';
            document.getElementById('quickEntryForm').appendChild(nextInput);
        }
        document.getElementById('quickEntryForm').submit();
    }

    // Client-side image & PDF preview and interactive controls
    const docInput = document.getElementById('scannedDocumentInput');
    const imgPreview = document.getElementById('scannedImagePreview');
    const pdfFrame = document.getElementById('pdfPreviewFrame');
    const placeholder = document.getElementById('uploadPlaceholder');
    const toolbar = document.getElementById('viewerToolbar');
    const container = document.getElementById('previewContainer');

    let currentZoom = 1.0;
    let currentRotate = 0;
    let isPanning = false;
    let startX = 0, startY = 0;
    let translateX = 0, translateY = 0;

    docInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const fileType = file.type;
        const fileUrl = URL.createObjectURL(file);

        if (fileType === 'application/pdf') {
            imgPreview.style.display = 'none';
            placeholder.style.display = 'none';
            toolbar.style.display = 'none';
            pdfFrame.src = fileUrl;
            pdfFrame.style.display = 'block';
        } else {
            pdfFrame.style.display = 'none';
            placeholder.style.display = 'none';
            toolbar.style.display = 'flex';
            imgPreview.src = fileUrl;
            imgPreview.style.display = 'block';
            resetTransform();
        }
    });

    function updateTransform() {
        imgPreview.style.transform = `translate(${translateX}px, ${translateY}px) scale(${currentZoom}) rotate(${currentRotate}deg)`;
    }

    function resetTransform() {
        currentZoom = 1.0;
        currentRotate = 0;
        translateX = 0;
        translateY = 0;
        updateTransform();
    }

    document.getElementById('btnZoomIn').addEventListener('click', () => {
        currentZoom = Math.min(currentZoom + 0.25, 4.0);
        updateTransform();
    });

    document.getElementById('btnZoomOut').addEventListener('click', () => {
        currentZoom = Math.max(currentZoom - 0.25, 0.5);
        updateTransform();
    });

    document.getElementById('btnRotate').addEventListener('click', () => {
        currentRotate = (currentRotate + 90) % 360;
        updateTransform();
    });

    document.getElementById('btnReset').addEventListener('click', resetTransform);

    // Pan with mouse drag
    container.addEventListener('mousedown', (e) => {
        if (imgPreview.style.display === 'none') return;
        isPanning = true;
        startX = e.clientX - translateX;
        startY = e.clientY - translateY;
        container.style.cursor = 'grabbing';
    });

    window.addEventListener('mousemove', (e) => {
        if (!isPanning) return;
        translateX = e.clientX - startX;
        translateY = e.clientY - startY;
        updateTransform();
    });

    window.addEventListener('mouseup', () => {
        isPanning = false;
        container.style.cursor = 'grab';
    });
</script>
@endpush
@endsection

