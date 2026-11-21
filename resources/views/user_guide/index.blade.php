@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- Top Hero Header --}}
    <div class="card mb-3 border-0 shadow-sm bg-primary text-white" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center mb-2 flex-wrap gap-2">
                        <span class="badge bg-warning text-dark fs-11 fw-bold text-uppercase px-2 py-1"><i class="fas fa-book-reader me-1"></i> Interactive Operations Manual</span>
                        <span class="badge bg-light text-primary fs-11 fw-bold px-2 py-1">OCEI Data Archive v2.0 Enterprise</span>
                    </div>
                    <h2 class="text-white fw-bold mb-2">System User Guide & Operational Playbook</h2>
                    <p class="text-light fs-10 mb-3 opacity-90" style="max-width: 720px;">
                        Interactive, step-by-step documentation for all user roles: <strong>Data Entry Operators</strong>, <strong>Office Assistants</strong>, <strong>Secretaries</strong>, and <strong>Administrators</strong>. Learn how to perform single & multi-tab entries, track application lifecycles, handle rejections, and verify statutory records.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-sm btn-light text-primary fw-bold" onclick="filterByRole('all')"><i class="fas fa-th-large me-1"></i> View All</button>
                        <button class="btn btn-sm btn-outline-light" onclick="filterByRole('operator')"><i class="fas fa-keyboard me-1"></i> Operator Guide</button>
                        <button class="btn btn-sm btn-outline-light" onclick="filterByRole('verifier')"><i class="fas fa-user-check me-1"></i> Office Assistant Guide</button>
                        <button class="btn btn-sm btn-outline-light" onclick="filterByRole('approver')"><i class="fas fa-stamp me-1"></i> Secretary Guide</button>
                        <button class="btn btn-sm btn-outline-light" onclick="filterByRole('admin')"><i class="fas fa-user-shield me-1"></i> Admin / Chairman</button>
                        <button class="btn btn-sm btn-falcon-default" onclick="window.print()"><i class="fas fa-print me-1"></i> Print / PDF</button>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <div class="bg-white bg-opacity-10 p-3 rounded-3 text-start border border-white border-opacity-25">
                        <div class="text-uppercase text-warning fs-11 fw-bold mb-2"><i class="fas fa-search me-1"></i> Search Guide Topics</div>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="guideSearchInput" class="form-control border-0" placeholder="Type keyword (e.g. quick entry, reject, 5 tab)..." onkeyup="searchGuide()">
                        </div>
                        <small class="text-white-50 fs-11 mt-1 d-block">Instant real-time topic filtering</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Interactive Lifecycle Simulator Bar --}}
    <div class="card mb-3 border-0 shadow-sm" id="section-lifecycle">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-900 fw-bold"><i class="fas fa-traffic-light text-primary me-2"></i> Interactive Application Lifecycle (Click any badge to inspect)</h6>
            <span class="badge bg-secondary-subtle text-secondary fs-11">5-Stage State Machine</span>
        </div>
        <div class="card-body p-3">
            <div class="row g-2 text-center" id="lifecycleStepper">
                <div class="col">
                    <div class="p-2 border rounded cursor-pointer lifecycle-step active-step" onclick="showLifecycleInfo('draft')" id="step-draft" style="cursor: pointer;">
                        <span class="badge bg-secondary mb-1">Stage 1</span>
                        <div class="fw-bold fs-10 text-dark">Draft</div>
                        <small class="text-muted fs-11">Operator Workspace</small>
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center text-muted"><i class="fas fa-chevron-right"></i></div>
                <div class="col">
                    <div class="p-2 border rounded cursor-pointer lifecycle-step" onclick="showLifecycleInfo('submitted')" id="step-submitted" style="cursor: pointer;">
                        <span class="badge bg-info mb-1">Stage 2</span>
                        <div class="fw-bold fs-10 text-dark">Submitted</div>
                        <small class="text-muted fs-11">Queued for Verification</small>
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center text-muted"><i class="fas fa-chevron-right"></i></div>
                <div class="col">
                    <div class="p-2 border rounded cursor-pointer lifecycle-step" onclick="showLifecycleInfo('rejected')" id="step-rejected" style="cursor: pointer;">
                        <span class="badge bg-danger mb-1">Exception</span>
                        <div class="fw-bold fs-10 text-dark">Rejected</div>
                        <small class="text-muted fs-11">Audit Feedback Loop</small>
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center text-muted"><i class="fas fa-chevron-right"></i></div>
                <div class="col">
                    <div class="p-2 border rounded cursor-pointer lifecycle-step" onclick="showLifecycleInfo('oa_approved')" id="step-oa_approved" style="cursor: pointer;">
                        <span class="badge bg-primary mb-1">Stage 3</span>
                        <div class="fw-bold fs-10 text-dark">OA Verified</div>
                        <small class="text-muted fs-11">Forwarded to Secretary</small>
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center text-muted"><i class="fas fa-chevron-right"></i></div>
                <div class="col">
                    <div class="p-2 border rounded cursor-pointer lifecycle-step" onclick="showLifecycleInfo('final_approved')" id="step-final_approved" style="cursor: pointer;">
                        <span class="badge bg-success mb-1">Stage 4</span>
                        <div class="fw-bold fs-10 text-dark">Final Approved</div>
                        <small class="text-muted fs-11">Permanently Indexed</small>
                    </div>
                </div>
            </div>

            {{-- Dynamic Lifecycle Explanation Box --}}
            <div class="alert alert-subtle-primary border mt-3 mb-0" id="lifecycleDetailsBox">
                <div class="d-flex align-items-start">
                    <span class="fas fa-info-circle fs-8 text-primary me-2 mt-1"></span>
                    <div>
                        <strong class="text-dark" id="lifecycleTitle">Status: Draft (draft)</strong>
                        <p class="fs-10 text-700 mb-1" id="lifecycleDesc">
                            The application is saved locally by the Data Entry Operator. It is <strong>NOT</strong> yet visible in the Office Assistant verification queue. The operator can freely edit, update all tabs, add attachments, or delete the record.
                        </p>
                        <div class="fs-11 text-600">
                            <strong>Where to find:</strong> <span class="badge bg-light text-dark border me-2">Applications &rarr; Applications List</span>
                            <strong>Can Operator Edit?</strong> <span class="badge bg-success text-white">Yes, Fully Editable</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION: DATA ENTRY OPERATOR PLAYBOOK --}}
    <div class="guide-role-section" data-role="operator">
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white"><i class="fas fa-keyboard me-2"></i> Section 1: Data Entry Operator (DEO) Complete Playbook</h5>
                <span class="badge bg-light text-primary fw-bold">Primary Role</span>
            </div>
            <div class="card-body">
                
                {{-- Subsection 1.1: Where to find entries & what queues mean --}}
                <div class="guide-item mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fas fa-folder-open text-primary me-1"></i> 1.1 Where to Find Entries & What Each Queue Means</h6>
                    <p class="fs-10 text-700">As a Data Entry Operator, your workspace in the left sidebar gives you 4 primary queues for every permit category (Electrician, Supervisor, Contractor):</p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-md-3">
                            <div class="card border h-100 shadow-none bg-light">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge bg-secondary me-2">Queue 1</span>
                                        <strong class="fs-10 text-dark">Applications List</strong>
                                    </div>
                                    <small class="text-600 d-block fs-11">URL: <code>/ex-{type}/operator</code></small>
                                    <p class="fs-11 text-700 mt-1 mb-0">Contains <strong>all records</strong> entered by you. Shows drafts, submitted, rejected, and approved with color-coded status badges.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border h-100 shadow-none bg-light">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge bg-info text-white me-2">Queue 2</span>
                                        <strong class="fs-10 text-dark">Pending Approval</strong>
                                    </div>
                                    <small class="text-600 d-block fs-11">URL: <code>/ex-{type}/operator/pending</code></small>
                                    <p class="fs-11 text-700 mt-1 mb-0">Contains records you submitted that are currently under review by the Office Assistant or Secretary. <strong>Locked from editing</strong>.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border h-100 shadow-none bg-light">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge bg-danger text-white me-2">Queue 3</span>
                                        <strong class="fs-10 text-dark">My Rejected</strong>
                                    </div>
                                    <small class="text-600 d-block fs-11">URL: <code>/ex-{type}/operator/rejected</code></small>
                                    <p class="fs-11 text-700 mt-1 mb-0">Contains applications sent back by Office Assistant or Secretary with audit notes. Click <strong>Edit</strong> to fix errors and resubmit.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border h-100 shadow-none bg-light">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge bg-success text-white me-2">Queue 4</span>
                                        <strong class="fs-10 text-dark">My Approved</strong>
                                    </div>
                                    <small class="text-600 d-block fs-11">URL: <code>/ex-{type}/operator/approved</code></small>
                                    <p class="fs-11 text-700 mt-1 mb-0">Completed, final verified archives. Displays official certificate numbers and timestamp of Secretary approval.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subsection 1.2: Two Form Modes (Quick vs 5-Tab) --}}
                <div class="guide-item mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fas fa-th-list text-primary me-1"></i> 1.2 Two Entry Modes: Quick Data Entry vs 5-Tab Detailed Form</h6>
                    
                    <ul class="nav nav-pills mb-3" id="entryModeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold btn-sm" id="mode-quick-tab" data-bs-toggle="pill" data-bs-target="#mode-quick" type="button" role="tab">
                                <i class="fas fa-bolt text-warning me-1"></i> Mode A: Quick Data Entry (Split-Screen Scanner)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold btn-sm" id="mode-5tab-tab" data-bs-toggle="pill" data-bs-target="#mode-5tab" type="button" role="tab">
                                <i class="fas fa-columns text-info me-1"></i> Mode B: 5-Tab Comprehensive Form
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content border rounded p-3 bg-light">
                        {{-- QUICK ENTRY TAB --}}
                        <div class="tab-pane fade show active" id="mode-quick" role="tabpanel">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-7">
                                    <h6 class="text-primary fw-bold mb-1">Split-Screen Side-by-Side Scanner Entry</h6>
                                    <p class="fs-10 text-700 mb-2">Designed for rapid volume data transcription directly from handwritten register book scans without switching browser windows.</p>
                                    <ol class="fs-10 text-700 ps-3 mb-0">
                                        <li><strong>Upload Scan:</strong> Drag & drop your register scan into the Left Pane viewer. Supports PDF, JPEG, PNG, WebP up to 10MB.</li>
                                        <li><strong>Use Viewer Controls:</strong> Use <code>Zoom In (+)</code>, <code>Zoom Out (-)</code>, <code>Rotate 90°</code>, and <code>Pan</code> to inspect handwriting.</li>
                                        <li><strong>Fill Key Fields:</strong> Transcribe Book #, Old Certificate #, Class (A, B, C, ABC), Applicant Name (English & Bengali), and Father's Name.</li>
                                        <li><strong>Choose Action:</strong>
                                            <ul class="mb-0">
                                                <li><code>Save as Draft</code>: Saves your progress; application stays in your private workspace.</li>
                                                <li><code>Submit for Approval</code>: Sends directly to Office Assistant verification queue.</li>
                                            </ul>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-md-5">
                                    <div class="border rounded bg-white p-2 shadow-sm text-center">
                                        <span class="badge bg-primary mb-2">Interactive Quick Form Layout</span>
                                        <div class="row g-1 text-center" style="font-size: 11px;">
                                            <div class="col-6 bg-light p-3 border rounded">
                                                <i class="fas fa-file-image fs-6 text-primary d-block mb-1"></i>
                                                <strong>Left Half:</strong><br>
                                                Register Scan Viewer with Zoom & Rotate
                                            </div>
                                            <div class="col-6 bg-light p-3 border rounded">
                                                <i class="fas fa-edit fs-6 text-success d-block mb-1"></i>
                                                <strong>Right Half:</strong><br>
                                                Fast Transcription Inputs & Submit
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 5-TAB DETAILED FORM --}}
                        <div class="tab-pane fade" id="mode-5tab" role="tabpanel">
                            <h6 class="text-primary fw-bold mb-2">Comprehensive 5-Tab Deep Archival Form</h6>
                            <p class="fs-10 text-700 mb-3">Used when recording complete personal, educational, employment, and multi-document records for a licensee:</p>
                            
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="card border h-100 shadow-none bg-white">
                                        <div class="card-body p-2">
                                            <span class="badge bg-primary mb-1">Tab 1</span>
                                            <h6 class="fs-10 fw-bold mb-1">Personal Details</h6>
                                            <ul class="fs-11 text-700 ps-3 mb-0">
                                                <li>Old Certificate # (Unique ID)</li>
                                                <li>Book # & Class (A, B, C, ABC)</li>
                                                <li>Applicant Name (English & Bengali)</li>
                                                <li>Father & Mother's Names</li>
                                                <li>NID Number & Date of Birth</li>
                                                <li>Mobile Number & Email</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border h-100 shadow-none bg-white">
                                        <div class="card-body p-2">
                                            <span class="badge bg-info text-white mb-1">Tab 2</span>
                                            <h6 class="fs-10 fw-bold mb-1">Address Details</h6>
                                            <ul class="fs-11 text-700 ps-3 mb-0">
                                                <li>Village / Holding / Road</li>
                                                <li>Post Office & Post Code</li>
                                                <li>Upazilla / Thana</li>
                                                <li>District & Division</li>
                                                <li>Present & Permanent addresses</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border h-100 shadow-none bg-white">
                                        <div class="card-body p-2">
                                            <span class="badge bg-warning text-dark mb-1">Tab 3</span>
                                            <h6 class="fs-10 fw-bold mb-1">Academic Education</h6>
                                            <ul class="fs-11 text-700 ps-3 mb-0">
                                                <li>Degree Level (SSC, HSC, Diploma, BSc)</li>
                                                <li>Subject / Major (Electrical)</li>
                                                <li>Board / University Name</li>
                                                <li>Passing Year & GPA / Division</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <div class="card border h-100 shadow-none bg-white">
                                        <div class="card-body p-2">
                                            <span class="badge bg-secondary mb-1">Tab 4</span>
                                            <h6 class="fs-10 fw-bold mb-1">Work & Industrial Experience</h6>
                                            <ul class="fs-11 text-700 ps-3 mb-0">
                                                <li>Company / Factory Name</li>
                                                <li>Designation (Senior Electrician, Maintenance Eng.)</li>
                                                <li>Joining Date, Relieving Date, Total Job Duration</li>
                                                <li>Key electrical job responsibilities</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <div class="card border h-100 shadow-none bg-white">
                                        <div class="card-body p-2">
                                            <span class="badge bg-danger mb-1">Tab 5</span>
                                            <h6 class="fs-10 fw-bold mb-1">Certificate Proof & Attachments</h6>
                                            <ul class="fs-11 text-700 ps-3 mb-0">
                                                <li>Issue Date & Expiry Date</li>
                                                <li>Exam Center & Result Grade</li>
                                                <li><strong>Mandatory Scanned Attachment:</strong> Upload high-res ledger scan. Cannot submit without at least one proof file.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subsection 1.3: Step-by-Step for 3 Permit Types --}}
                <div class="guide-item mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fas fa-layer-group text-primary me-1"></i> 1.3 Step-by-Step Entry by Category (Electrician, Supervisor, Contractor)</h6>
                    
                    <div class="accordion" id="categoryAccordion">
                        {{-- Electrician --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-bold text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseElec">
                                    <i class="fas fa-bolt text-warning me-2"></i> Electrician Permit Entry Specifics
                                </button>
                            </h2>
                            <div id="collapseElec" class="accordion-collapse collapse show" data-bs-parent="#categoryAccordion">
                                <div class="accordion-body fs-10 text-700">
                                    <ol class="mb-0 ps-3">
                                        <li>Navigate to <code>Electrician &rarr; Quick Entry</code> or <code>Electrician &rarr; Create Application</code>.</li>
                                        <li>Locate the <strong>Class</strong> field and select from <code>A</code>, <code>B</code>, <code>C</code>, or compound combinations like <code>ABC</code>.</li>
                                        <li>Transcribe candidate name in both English capital letters and clear Bengali font.</li>
                                        <li>Upload the scanned certificate or ledger register.</li>
                                        <li>Click <strong>Submit for Approval</strong> to push to Office Assistant.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        {{-- Supervisor --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-warning" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSup">
                                    <i class="fas fa-user-tie text-warning me-2"></i> Supervisor Certificate Entry Specifics
                                </button>
                            </h2>
                            <div id="collapseSup" class="accordion-collapse collapse" data-bs-parent="#categoryAccordion">
                                <div class="accordion-body fs-10 text-700">
                                    <ol class="mb-0 ps-3">
                                        <li>Navigate to <code>Supervisor &rarr; Quick Entry</code> or <code>Supervisor &rarr; Create Application</code>.</li>
                                        <li>Enter Supervisor registration code in <code>old_certificate_number</code>.</li>
                                        <li>In Tab 3 (Education), enter details of the Diploma/BSc in Electrical Engineering.</li>
                                        <li>Ensure register scan clearly displays Board seal and signature.</li>
                                        <li>Click <strong>Submit for Approval</strong>.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        {{-- Contractor --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-danger" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCont">
                                    <i class="fas fa-building text-danger me-2"></i> Contractor License Entry Specifics (Corporate Entity)
                                </button>
                            </h2>
                            <div id="collapseCont" class="accordion-collapse collapse" data-bs-parent="#categoryAccordion">
                                <div class="accordion-body fs-10 text-700">
                                    <ol class="mb-0 ps-3">
                                        <li>Navigate to <code>Contractor &rarr; Quick Entry</code> or <code>Contractor &rarr; Create Application</code>.</li>
                                        <li>Enter <strong>Company Name (English)</strong> and <strong>Company Name (Bengali)</strong>.</li>
                                        <li>Provide the official <strong>Trade License Number</strong> (Indexed for instant search).</li>
                                        <li>Select Company Type (Proprietorship, Partnership, Private Limited).</li>
                                        <li>Enter associated <strong>Certified Supervisor Certificate Number</strong>.</li>
                                        <li>Enter both Head Office Address (BOA) and Branch Office Address (BRA) if applicable.</li>
                                        <li>Attach Trade License copy and ledger book scan, then click <strong>Submit for Approval</strong>.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subsection 1.4: How to handle Rejections --}}
                <div class="guide-item">
                    <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fas fa-undo-alt text-danger me-1"></i> 1.4 How to Handle and Fix Rejected Applications</h6>
                    <div class="alert alert-warning border-2 p-3 mb-0">
                        <div class="d-flex align-items-start">
                            <span class="fas fa-exclamation-circle fs-8 text-warning me-2 mt-1"></span>
                            <div>
                                <strong class="text-dark">When an application is rejected by Office Assistant or Secretary:</strong>
                                <ul class="fs-10 text-700 mb-2 mt-1 ps-3">
                                    <li>You will receive an instant bell notification at the top right of the navigation bar.</li>
                                    <li>Navigate to <code>Electrician / Supervisor / Contractor &rarr; My Rejected</code>.</li>
                                    <li>The table displays the exact <strong>Reject Reason</strong> supplied by the verifier (e.g. <em>"Name spelling does not match ledger page 44"</em>).</li>
                                    <li>Click the <span class="badge bg-primary"><i class="fas fa-edit"></i> Edit</span> button next to the application.</li>
                                    <li>Correct the flagged fields or re-upload clearer scanned images in Tab 5.</li>
                                    <li>Click <strong>Submit Application</strong>. The application moves back to <code>submitted_to_office_assistant</code> for re-verification!</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- SECTION: OFFICE ASSISTANT (VERIFIER) PLAYBOOK --}}
    <div class="guide-role-section" data-role="verifier">
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-warning text-dark py-2 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark"><i class="fas fa-user-check me-2"></i> Section 2: Office Assistant (Verification Officer) Playbook</h5>
                <span class="badge bg-dark text-white fw-bold">First-Level Verifier</span>
            </div>
            <div class="card-body">
                
                <div class="guide-item mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fas fa-inbox text-warning me-1"></i> 2.1 Accessing the Verification Queue</h6>
                    <p class="fs-10 text-700">Navigate to <code>{Category} &rarr; Verification &rarr; Pending Queue</code>. All applications submitted by operators appear here in chronological order.</p>
                    <div class="alert alert-light border p-2 fs-10 text-600">
                        <i class="fas fa-filter text-primary me-1"></i> Use the table header filters to filter by Date Range, Class, or Search by Old Certificate Number.
                    </div>
                </div>

                <div class="guide-item mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fas fa-search-plus text-warning me-1"></i> 2.2 Side-by-Side Verification Screen</h6>
                    <p class="fs-10 text-700">Click <strong>Review</strong> on any pending record. The review screen shows:</p>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <div class="p-2 border rounded bg-light fs-11">
                                <strong>Left Side: Scanned Proof Document</strong><br>
                                Inspect the original historical physical register scan. Check book number, applicant name, and exam result.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 border rounded bg-light fs-11">
                                <strong>Right Side: Digital Data Transcription</strong><br>
                                Review applicant name, Bengali spelling, father's name, class, and issue dates transcribed by the operator.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="guide-item mb-3">
                    <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fas fa-check-double text-warning me-1"></i> 2.3 Taking Action: Approve vs. Reject</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border-success h-100 shadow-none">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge bg-success me-2"><i class="fas fa-check"></i> Action 1</span>
                                        <strong class="text-success fs-10">Approve & Forward</strong>
                                    </div>
                                    <p class="fs-11 text-700 mb-2">If all data matches the scanned ledger: Click the green <strong>"Approve Application"</strong> button.</p>
                                    <div class="fs-11 text-600 bg-light p-2 rounded">
                                        &bull; Application status updates to <code>submitted_to_secretary</code>.<br>
                                        &bull; Automatic notification forwarded to Secretary for final audit.<br>
                                        &bull; Moves to your <code>My Approved</code> list.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-danger h-100 shadow-none">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge bg-danger me-2"><i class="fas fa-times"></i> Action 2</span>
                                        <strong class="text-danger fs-10">Reject with Audit Reason</strong>
                                    </div>
                                    <p class="fs-11 text-700 mb-2">If data is mismatched or scan is blurry: Click <strong>"Reject Application"</strong>.</p>
                                    <div class="fs-11 text-600 bg-light p-2 rounded">
                                        &bull; Modal prompt opens requiring mandatory rejection explanation.<br>
                                        &bull; Application status updates to <code>office_assistant_rejected</code>.<br>
                                        &bull; Operator is notified with the exact instructions for correction.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bulk Verification --}}
                <div class="guide-item">
                    <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fas fa-tasks text-warning me-1"></i> 2.4 Batch / Bulk Verification</h6>
                    <p class="fs-10 text-700 mb-0">
                        In the pending table, select the checkboxes next to multiple reviewed applications and use the <strong>"Bulk Approve"</strong> toolbar button at the top to endorse up to 50 applications in a single transaction.
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- SECTION: SECRETARY (APPROVER) PLAYBOOK --}}
    <div class="guide-role-section" data-role="approver">
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-success text-white py-2 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white"><i class="fas fa-stamp me-2"></i> Section 3: Secretary (Approver) Playbook</h5>
                <span class="badge bg-light text-success fw-bold">Statutory Approval Authority</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-7">
                        <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fas fa-clipboard-check text-success me-1"></i> 3.1 Executive Approval Workflow</h6>
                        <ol class="fs-10 text-700 ps-3 mb-2">
                            <li>Navigate to <code>{Category} &rarr; Approval (Secretary) &rarr; Pending</code>.</li>
                            <li>Open application to inspect both the operator transcription and the Office Assistant verification stamp.</li>
                            <li>Click <strong>Final Approve</strong>:
                                <ul class="mb-1">
                                    <li>Locks the archive record permanently from all future modifications.</li>
                                    <li>Assigns official approval timestamp and digital audit certificate signature.</li>
                                    <li>Status transitions to <code>secretary_approved_final</code>.</li>
                                    <li>Record becomes immediately searchable in Chairman inquiry and official KPI reports.</li>
                                </ul>
                            </li>
                            <li>If executive issues arise: Click <strong>Reject</strong> and supply reason. Record routes back to operator for correction.</li>
                        </ol>
                    </div>
                    <div class="col-md-5">
                        <div class="border rounded p-3 bg-light text-center">
                            <span class="badge bg-success mb-2">Permanent Archive Lock</span>
                            <div class="text-start fs-11 text-600">
                                <div><i class="fas fa-lock text-danger me-1"></i> <strong>Immutability:</strong> Once approved, neither operators nor assistants can alter records.</div>
                                <div class="mt-2"><i class="fas fa-database text-primary me-1"></i> <strong>Search Engine:</strong> Automatically indexed for instantaneous multi-column lookup.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION: CHAIRMAN & ADMIN PLAYBOOK --}}
    <div class="guide-role-section" data-role="admin">
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-dark text-white py-2 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white"><i class="fas fa-user-shield me-2"></i> Section 4: Chairman & System Admin Playbook</h5>
                <span class="badge bg-warning text-dark fw-bold">Executive Oversight</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card border h-100 shadow-none bg-light">
                            <div class="card-body p-2">
                                <h6 class="fs-10 fw-bold text-primary mb-1"><i class="fas fa-search me-1"></i> Global Inquiry Archive</h6>
                                <p class="fs-11 text-700 mb-0">Chairman can view and search across all approved records in Bangladesh for Electricians, Supervisors, and Contractors. Supports filtering by Division, District, Year, and Book #.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border h-100 shadow-none bg-light">
                            <div class="card-body p-2">
                                <h6 class="fs-10 fw-bold text-success mb-1"><i class="fas fa-chart-line me-1"></i> Analytical KPI Reports</h6>
                                <p class="fs-11 text-700 mb-0">Access <code>/reports/kpi</code>, <code>/reports/operator</code>, and <code>/reports/office-assistant</code> to track operator digitization velocity, verification backlog, and approval throughput.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border h-100 shadow-none bg-light">
                            <div class="card-body p-2">
                                <h6 class="fs-10 fw-bold text-danger mb-1"><i class="fas fa-tools me-1"></i> Admin Overrides & Audits</h6>
                                <p class="fs-11 text-700 mb-0">Super Admin can restore soft-deleted records, inspect the immutable <code>RecordHistory</code> audit table, reset passwords, and manage staff user accounts.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION: ANNOTATED UI SCREENSHOTS & SAMPLE REGISTER SCANS --}}
    <div class="card mb-3 border-0 shadow-sm" id="section-annotated-scans">
        <div class="card-header bg-light py-2">
            <h5 class="mb-0 text-900"><i class="fas fa-file-invoice text-success me-2"></i> Section 5: Annotated Register Scans & Transcription Guidance</h5>
        </div>
        <div class="card-body">
            <p class="fs-10 text-700 mb-3">
                Below are authentic historical register book pages annotated with numbered hotspot callouts explaining the exact transcription coordinates:
            </p>

            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card border h-100 shadow-none">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <strong class="fs-10 text-primary"><i class="fas fa-bolt text-warning me-1"></i> Electrician Historical Ledger (Annotated)</strong>
                            <span class="badge bg-primary">Sample Scan #1</span>
                        </div>
                        <div class="card-body p-2 text-center">
                            <img src="{{ asset('assets/img/guide/media_1793421353799_e7be040e.jpg') }}" alt="Electrician Ledger" class="img-fluid rounded border mb-2 shadow-sm" style="max-height: 300px; object-fit: contain;">
                            <div class="text-start fs-11 p-2 bg-light rounded">
                                <div class="mb-1"><span class="badge bg-danger">Callout (1)</span> <strong>Book Number:</strong> Top right corner ledger number (e.g., <code>BK-12</code>).</div>
                                <div class="mb-1"><span class="badge bg-primary">Callout (2)</span> <strong>Old Certificate Number:</strong> Column 1 certificate ID (e.g., <code>EL-48291</code>).</div>
                                <div class="mb-1"><span class="badge bg-success">Callout (3)</span> <strong>Name Fields:</strong> Transcribe candidate name into both English & Bengali fields.</div>
                                <div><span class="badge bg-warning text-dark">Callout (4)</span> <strong>Permit Class:</strong> Validated class letter (<code>A</code>, <code>B</code>, <code>C</code>, or <code>ABC</code>).</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border h-100 shadow-none">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <strong class="fs-10 text-warning"><i class="fas fa-user-tie text-warning me-1"></i> Supervisor Historical Ledger (Annotated)</strong>
                            <span class="badge bg-warning text-dark">Sample Scan #2</span>
                        </div>
                        <div class="card-body p-2 text-center">
                            <img src="{{ asset('assets/img/guide/media_1793421353796_2cdf6736.jpg') }}" alt="Supervisor Ledger" class="img-fluid rounded border mb-2 shadow-sm" style="max-height: 300px; object-fit: contain;">
                            <div class="text-start fs-11 p-2 bg-light rounded">
                                <div class="mb-1"><span class="badge bg-danger">Callout (A)</span> <strong>Supervisor ID:</strong> Registration code issued by Electrical Licensing Board.</div>
                                <div class="mb-1"><span class="badge bg-info">Callout (B)</span> <strong>Academic Degree:</strong> Diploma/Degree title & passing institute.</div>
                                <div class="mb-1"><span class="badge bg-success">Callout (C)</span> <strong>Dates:</strong> Statutory Issue and Expiry timestamps.</div>
                                <div><span class="badge bg-secondary">Callout (D)</span> <strong>Board Seal:</strong> Official signature of the Licensing Authority.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Interactive JavaScript Controller for User Guide --}}
<script>
    // Lifecycle data dictionary
    const lifecycleData = {
        draft: {
            title: "Status: Draft (draft)",
            desc: "The application is saved in the Data Entry Operator's private workspace. It is NOT yet submitted to Office Assistant. The operator can edit all tabs, add attachments, or delete the record.",
            where: "Applications List / Drafts",
            editable: "Yes, fully editable by operator"
        },
        submitted: {
            title: "Status: Submitted to Office Assistant (submitted_to_office_assistant)",
            desc: "The application is queued in the Office Assistant's Pending Verification list. It is locked from operator modification to prevent race conditions during verification.",
            where: "Operator: Pending Approval | Office Assistant: Pending Verification",
            editable: "No (Locked during review)"
        },
        rejected: {
            title: "Status: Rejected (office_assistant_rejected / secretary_rejected)",
            desc: "A verifier found errors (e.g. blurry scan, name mismatch). The exact rejection notes are recorded and sent to the operator. The operator can edit the application to fix issues and resubmit.",
            where: "Operator: My Rejected Queue",
            editable: "Yes (Unlocked for corrections)"
        },
        oa_approved: {
            title: "Status: OA Verified (submitted_to_secretary)",
            desc: "The Office Assistant verified the application against the scanned proof document and endorsed it. It is now awaiting statutory executive review in the Secretary's queue.",
            where: "Secretary: Pending Approval Queue",
            editable: "No (Under executive review)"
        },
        final_approved: {
            title: "Status: Final Approved (secretary_approved_final)",
            desc: "The Secretary granted statutory approval. The record is permanently locked into the digital archive, indexed across all search parameters, and available in public/Chairman searches.",
            where: "Operator: My Approved | Chairman: Approved Records",
            editable: "No (Permanently Archived)"
        }
    };

    function showLifecycleInfo(statusKey) {
        const info = lifecycleData[statusKey];
        if (!info) return;

        document.getElementById('lifecycleTitle').textContent = info.title;
        document.getElementById('lifecycleDesc').textContent = info.desc;

        document.querySelectorAll('.lifecycle-step').forEach(el => el.classList.remove('active-step', 'border-primary', 'bg-primary-subtle'));
        const activeEl = document.getElementById('step-' + statusKey);
        if (activeEl) {
            activeEl.classList.add('active-step', 'border-primary', 'bg-primary-subtle');
        }
    }

    function filterByRole(role) {
        const sections = document.querySelectorAll('.guide-role-section');
        if (role === 'all') {
            sections.forEach(s => s.style.display = 'block');
        } else {
            sections.forEach(s => {
                if (s.getAttribute('data-role') === role) {
                    s.style.display = 'block';
                } else {
                    s.style.display = 'none';
                }
            });
        }
    }

    function searchGuide() {
        const query = document.getElementById('guideSearchInput').value.toLowerCase();
        const items = document.querySelectorAll('.guide-item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = query.length > 2 ? 'none' : 'block';
            }
        });
    }
</script>

<style>
    .cursor-pointer { cursor: pointer; }
    .lifecycle-step:hover { background-color: #f8f9fa; transform: translateY(-2px); transition: all 0.2s ease-in-out; }
    .active-step { border: 2px solid #2c7be5 !important; background-color: #edf2f9; }
</style>
@endsection
