@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">
    {{-- Header Banner --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 text-primary">
                <i class="fas fa-building text-warning me-2"></i>Contractor License Archive – Quick Data Entry
            </h4>
            <span class="text-muted small">
                Transcribe handwritten electrical contractor license register page details into the archive with real-time scan inspection.
            </span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ex-contractor.operator.create') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-columns me-1"></i> Switch to 5-Tab Form
            </a>
            <a href="{{ route('ex-contractor.operator.index') }}" class="btn btn-secondary btn-sm">
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
    <form method="POST" action="{{ route('ex-contractor.operator.quick-store') }}" enctype="multipart/form-data" id="quickEntryForm">
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
                            <div class="form-text small">Select the scanned physical contractor register page (JPG, PNG or PDF, max 10MB).</div>
                        </div>

                        {{-- Scan Preview Canvas Container --}}
                        <div id="previewContainer" class="border rounded bg-dark flex-grow-1 position-relative overflow-hidden d-flex align-items-center justify-content-center"
                            style="min-height: 520px; max-height: 720px; cursor: grab;">
                            
                            {{-- Placeholder when no image selected --}}
                            <div id="uploadPlaceholder" class="text-center text-secondary p-4">
                                <i class="fas fa-cloud-upload-alt fa-3x mb-2 text-muted"></i>
                                <p class="mb-1 fw-bold">Select a scanned contractor register file above</p>
                                <span class="small text-muted">The document will render here with zoom, rotate, and pan tools for manual data transcription.</span>
                            </div>

                            {{-- Image Display Element --}}
                            <img id="scannedImagePreview" src="" alt="Contractor Register Scan" 
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
                            {{-- Book Number & License Number --}}
                            <div class="col-md-5">
                                <label for="book_number" class="form-label small fw-bold mb-1">
                                    Book / Page No. (বই নম্বর / পৃষ্ঠা নং)
                                </label>
                                <input type="text" class="form-control form-control-sm @error('book_number') is-invalid @enderror"
                                    id="book_number" name="book_number" value="{{ old('book_number') }}"
                                    placeholder="e.g. 7351, 1881, 1880" autofocus>
                                @error('book_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-7">
                                <label for="old_certificate_number" class="form-label small fw-bold mb-1">
                                    License No. (লাইসেন্স নং সি) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm @error('old_certificate_number') is-invalid @enderror"
                                    id="old_certificate_number" name="old_certificate_number" value="{{ old('old_certificate_number') }}"
                                    placeholder="e.g. ৪০/১৮৮১, ৪০/১৮৮০, ১০/৭৩৫১" required>
                                @error('old_certificate_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Approved Class with Quick-Select Buttons --}}
                            <div class="col-12 mt-2">
                                <label for="class" class="form-label small fw-bold mb-1">
                                    Approved Class (অনুমোদিত কাজের শ্রেণী)
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

                            {{-- Firm / Contractor Name (Bangla & English) --}}
                            <div class="col-md-6 mt-2">
                                <label for="company_name_bn" class="form-label small fw-bold mb-1">
                                    Firm Name (মেসার্স - বাংলা)
                                </label>
                                <input type="text" class="form-control form-control-sm @error('company_name_bn') is-invalid @enderror"
                                    id="company_name_bn" name="company_name_bn" value="{{ old('company_name_bn') }}"
                                    placeholder="e.g. দি কসমস ইঞ্জিনিয়ার্স">
                                @error('company_name_bn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-2">
                                <label for="company_name_en" class="form-label small fw-bold mb-1">
                                    Firm Name (English)
                                </label>
                                <input type="text" class="form-control form-control-sm @error('company_name_en') is-invalid @enderror"
                                    id="company_name_en" name="company_name_en" value="{{ old('company_name_en') }}"
                                    placeholder="e.g. The Cosmos Engineers">
                                @error('company_name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Address (One Block Text) --}}
                            <div class="col-12 mt-2">
                                <label for="village" class="form-label small fw-bold mb-1">
                                    Business Address (ঠিকানা)
                                </label>
                                <textarea class="form-control form-control-sm @error('village') is-invalid @enderror"
                                    id="village" name="village" rows="2"
                                    placeholder="e.g. ৫, রূপসী হাউজিং সোসাইটি, নাসিরাবাদ, চট্টগ্রাম - ৪২০১">{{ old('village') }}</textarea>
                                @error('village')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Issue Date & Latest Expiry Date --}}
                            <div class="col-md-6 mt-2">
                                <label for="issue_date" class="form-label small fw-bold mb-1">
                                    Issue Date (লাইসেন্স তারিখ)
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

                            {{-- Assigned Supervisor --}}
                            <div class="col-12 mt-2">
                                <label for="elb_certified_supervisor_no" class="form-label small fw-bold mb-1">
                                    Assigned Supervisor(s) (নিযুক্ত সুপারভাইজার ও সার্টিফিকেট নং)
                                </label>
                                <input type="text" class="form-control form-control-sm @error('elb_certified_supervisor_no') is-invalid @enderror"
                                    id="elb_certified_supervisor_no" name="elb_certified_supervisor_no" value="{{ old('elb_certified_supervisor_no') }}"
                                    placeholder="e.g. অরুন কান্তি হাওলাদার, এস-৪২/১২০১/কখগ; মোঃ আবু ইউসুফ, এস-৩৬/৮৯৪/কখগ">
                                @error('elb_certified_supervisor_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Testing Equipment / Instruments --}}
                            <div class="col-12 mt-2">
                                <div class="p-2 border rounded bg-light">
                                    <span class="small fw-bold text-secondary d-block mb-2">
                                        <i class="fas fa-tools me-1"></i> Testing Instruments (যন্ত্রপাতির বিবরণ)
                                    </span>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label for="et_serial_no" class="form-label small mb-0">Earth Tester No. (আর্থ টেষ্টার)</label>
                                            <input type="text" class="form-control form-control-sm @error('et_serial_no') is-invalid @enderror"
                                                id="et_serial_no" name="et_serial_no" value="{{ old('et_serial_no') }}" placeholder="e.g. ০৭০">
                                            @error('et_serial_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="mg_serial_no" class="form-label small mb-0">Megger No. (মেগার নং)</label>
                                            <input type="text" class="form-control form-control-sm @error('mg_serial_no') is-invalid @enderror"
                                                id="mg_serial_no" name="mg_serial_no" value="{{ old('mg_serial_no') }}" placeholder="e.g. ০৩৬">
                                            @error('mg_serial_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cm_serial_no" class="form-label small mb-0">Clamp Meter No. (ক্লিপন মিটার)</label>
                                            <input type="text" class="form-control form-control-sm @error('cm_serial_no') is-invalid @enderror"
                                                id="cm_serial_no" name="cm_serial_no" value="{{ old('cm_serial_no') }}" placeholder="e.g. ০১১২০৬১">
                                            @error('cm_serial_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Optional Details: Mobile & Email --}}
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
                                <label for="owner_shareholder_name" class="form-label small fw-bold mb-1">
                                    Owner / Proprietor (মালিক / অংশীদার - optional)
                                </label>
                                <input type="text" class="form-control form-control-sm @error('owner_shareholder_name') is-invalid @enderror"
                                    id="owner_shareholder_name" name="owner_shareholder_name" value="{{ old('owner_shareholder_name') }}"
                                    placeholder="e.g. Proprietor Name">
                                @error('owner_shareholder_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                <a href="{{ route('ex-contractor.operator.index') }}" class="btn btn-outline-secondary btn-sm">
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

