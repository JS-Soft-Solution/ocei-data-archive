@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-body position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-primary-subtle text-primary fs-11 fw-semi-bold text-uppercase px-2 py-1 me-2">OCEI Archive v2.0 Enterprise</span>
                    <span class="badge bg-success-subtle text-success fs-11 fw-semi-bold px-2 py-1">PHP 8.4 JIT • Laravel 13 • MySQL 9</span>
                </div>
                <h3 class="text-primary mb-1">Official User Guide & Workflow Operations Manual</h3>
                <p class="text-700 fs-10 mb-3">
                    Comprehensive, step-by-step interactive documentation for legacy electrical licensing archives (Electricians, Supervisors, Contractors), verification lifecycles, and role-based operational procedures.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#section-roles" class="btn btn-sm btn-outline-primary"><i class="fas fa-users-cog me-1"></i> Role Matrix</a>
                    <a href="#section-electrician" class="btn btn-sm btn-outline-info"><i class="fas fa-bolt me-1"></i> Electrician Flow</a>
                    <a href="#section-supervisor" class="btn btn-sm btn-outline-warning"><i class="fas fa-user-tie me-1"></i> Supervisor Flow</a>
                    <a href="#section-contractor" class="btn btn-sm btn-outline-danger"><i class="fas fa-building me-1"></i> Contractor Flow</a>
                    <a href="#section-samples" class="btn btn-sm btn-outline-secondary"><i class="fas fa-file-image me-1"></i> Sample Scans</a>
                    <button class="btn btn-sm btn-falcon-default" onclick="window.print()"><i class="fas fa-print me-1"></i> Print Guide</button>
                </div>
            </div>
            <div class="col-lg-4 text-center d-none d-lg-block">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="fs-11 text-uppercase text-600 fw-bold mb-1">Environment Health</div>
                    <div class="d-flex justify-content-around text-center mt-2">
                        <div>
                            <div class="text-success fs-8 fw-bold">Active</div>
                            <small class="text-500 fs-11">Web (8095)</small>
                        </div>
                        <div class="border-start ps-3">
                            <div class="text-success fs-8 fw-bold">Active</div>
                            <small class="text-500 fs-11">DB (3308)</small>
                        </div>
                        <div class="border-start ps-3">
                            <div class="text-success fs-8 fw-bold">Active</div>
                            <small class="text-500 fs-11">PMA (8096)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 1: ROLE MATRIX --}}
<div class="card mb-3" id="section-roles">
    <div class="card-header bg-light">
        <h5 class="mb-0 text-900"><i class="fas fa-id-badge text-primary me-2"></i> 1. Role & Permission Governance</h5>
    </div>
    <div class="card-body">
        <p class="fs-10 text-700">The OCEI system operates under a strict four-eye principle to maintain legal integrity across all archival record digitizations.</p>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm fs-10 mb-0">
                <thead class="table-primary text-uppercase fs-11">
                    <tr>
                        <th style="width: 20%;">Role</th>
                        <th style="width: 25%;">Primary Accountabilities</th>
                        <th style="width: 35%;">Permissions & Actions</th>
                        <th style="width: 20%;">State Transitions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong class="text-dark">Data Entry Operator</strong><br>
                            <span class="badge bg-info-subtle text-info">data_entry_operator</span>
                        </td>
                        <td>Digitize physical ledger books, upload scanned proof documents, submit candidate data.</td>
                        <td>
                            <ul class="mb-0 ps-3">
                                <li>Quick single-page entry or detailed tabbed entry</li>
                                <li>Upload single/multi-page ledger scan (PDF, JPEG, PNG)</li>
                                <li>Edit drafts, claim records, batch submit</li>
                                <li>Revise rejected applications based on audit feedback</li>
                            </ul>
                        </td>
                        <td>
                            <code>draft</code> &rarr; <code>submitted_to_office_assistant</code>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong class="text-dark">Office Assistant (Verifier)</strong><br>
                            <span class="badge bg-warning-subtle text-warning">office_assistant</span>
                        </td>
                        <td>First-level verification against original ledger scan and statutory requirements.</td>
                        <td>
                            <ul class="mb-0 ps-3">
                                <li>Side-by-side ledger scan comparison tool</li>
                                <li>Approve application & forward to Secretary</li>
                                <li>Reject application with mandatory audit reason</li>
                                <li>Batch verification operations</li>
                            </ul>
                        </td>
                        <td>
                            <code>submitted_to_office_assistant</code> &rarr; <br>
                            &bull; <code>submitted_to_secretary</code> (Approve)<br>
                            &bull; <code>office_assistant_rejected</code> (Reject)
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong class="text-dark">Secretary (Approver)</strong><br>
                            <span class="badge bg-success-subtle text-success">secretary</span>
                        </td>
                        <td>Statutory approval authority for licensing register validation.</td>
                        <td>
                            <ul class="mb-0 ps-3">
                                <li>Second-level executive audit</li>
                                <li>Final approval lock into permanent archive</li>
                                <li>Reject back to operator with remarks</li>
                                <li>Access comprehensive analytical KPI reports</li>
                            </ul>
                        </td>
                        <td>
                            <code>submitted_to_secretary</code> &rarr; <br>
                            &bull; <code>secretary_approved_final</code> (Approved)<br>
                            &bull; <code>secretary_rejected</code> (Reject)
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong class="text-dark">Chairman & Super Admin</strong><br>
                            <span class="badge bg-danger-subtle text-danger">chairman / super_admin</span>
                        </td>
                        <td>System administration, record modifications, restore soft-deleted files, view-all.</td>
                        <td>
                            <ul class="mb-0 ps-3">
                                <li>Global ledger search across all classes</li>
                                <li>Audit logs & history tracking inspection</li>
                                <li>User account provisioning & role management</li>
                                <li>Direct status overrides in exception handling</li>
                            </ul>
                        </td>
                        <td>
                            Full administrative oversight & override
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- SECTION 2: WORKFLOW ARCHITECTURE --}}
<div class="card mb-3" id="section-workflows">
    <div class="card-header bg-light">
        <h5 class="mb-0 text-900"><i class="fas fa-project-diagram text-info me-2"></i> 2. End-to-End Workflow Lifecycles</h5>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs" id="workflowTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="electrician-tab" data-bs-toggle="tab" data-bs-target="#tab-electrician" type="button" role="tab"><i class="fas fa-bolt text-info me-1"></i> Electrician Permit</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="supervisor-tab" data-bs-toggle="tab" data-bs-target="#tab-supervisor" type="button" role="tab"><i class="fas fa-user-tie text-warning me-1"></i> Supervisor Certificate</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="contractor-tab" data-bs-toggle="tab" data-bs-target="#tab-contractor" type="button" role="tab"><i class="fas fa-building text-danger me-1"></i> Contractor License</button>
            </li>
        </ul>
        <div class="tab-content border-bottom border-start border-end p-3 bg-white" id="workflowTabContent">
            
            {{-- ELECTRICIAN TAB --}}
            <div class="tab-pane fade show active" id="tab-electrician" role="tabpanel">
                <h6 class="fw-bold text-primary mb-3">Electrician Permit Lifecycle Flowchart</h6>
                
                {{-- Diagrammatic Visual Pipeline --}}
                <div class="p-3 bg-light rounded-3 mb-4">
                    <div class="row g-2 text-center align-items-center">
                        <div class="col-md-3">
                            <div class="card border-primary h-100 shadow-none">
                                <div class="card-body p-2">
                                    <span class="badge bg-primary mb-1">Step 1: Entry</span>
                                    <h6 class="fs-10 fw-bold mb-1">Operator</h6>
                                    <p class="fs-11 text-600 mb-0">Enters permit #, applicant name, Father name, class (A, B, C) & attaches ledger scan.</p>
                                    <span class="badge bg-secondary-subtle text-secondary mt-2">status: draft</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 d-none d-md-block text-primary fs-7">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning h-100 shadow-none">
                                <div class="card-body p-2">
                                    <span class="badge bg-warning text-dark mb-1">Step 2: Verification</span>
                                    <h6 class="fs-10 fw-bold mb-1">Office Assistant</h6>
                                    <p class="fs-11 text-600 mb-0">Cross-checks entered fields with ledger scan. Rejects with notes or approves.</p>
                                    <span class="badge bg-warning-subtle text-dark mt-2">submitted_to_office_assistant</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 d-none d-md-block text-warning fs-7">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success h-100 shadow-none">
                                <div class="card-body p-2">
                                    <span class="badge bg-success mb-1">Step 3: Approval</span>
                                    <h6 class="fs-10 fw-bold mb-1">Secretary</h6>
                                    <p class="fs-11 text-600 mb-0">Conducts executive audit. Final approval permanently indexes the record.</p>
                                    <span class="badge bg-success-subtle text-success mt-2">secretary_approved_final</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-2 d-flex align-items-center mb-0" role="alert">
                    <div class="bg-info me-3 icon-item"><span class="fas fa-info-circle text-white fs-8"></span></div>
                    <p class="mb-0 fs-10">
                        <strong>Electrician Field Validation:</strong> Mandatory fields include <code>old_certificate_number</code> (unique), <code>book_number</code>, and <code>applicant_name_en</code>. The scanned image of the official register book is required before final submission.
                    </p>
                </div>
            </div>

            {{-- SUPERVISOR TAB --}}
            <div class="tab-pane fade" id="tab-supervisor" role="tabpanel">
                <h6 class="fw-bold text-warning mb-3">Supervisor Certificate Flow with Audit Rejection Loop</h6>
                
                <div class="p-3 bg-light rounded-3 mb-4">
                    <div class="row g-2 text-center align-items-center">
                        <div class="col-md-3">
                            <div class="card border-primary h-100 shadow-none">
                                <div class="card-body p-2">
                                    <span class="badge bg-primary mb-1">Entry</span>
                                    <h6 class="fs-10 fw-bold mb-1">DEO Entry</h6>
                                    <p class="fs-11 text-600 mb-0">Supervisor details, educational qualification & ledger proof.</p>
                                    <span class="badge bg-primary-subtle text-primary mt-2">draft &rarr; submitted</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 d-none d-md-block text-muted fs-7">
                            <i class="fas fa-arrows-alt-h"></i>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-danger h-100 shadow-none">
                                <div class="card-body p-2">
                                    <span class="badge bg-danger mb-1">Rejection & Revision Loop</span>
                                    <h6 class="fs-10 fw-bold mb-1">Assistant Rejection</h6>
                                    <p class="fs-11 text-600 mb-0">If discrepancy occurs, enters mandatory reason. DEO receives alert, revises & resubmits.</p>
                                    <span class="badge bg-danger-subtle text-danger mt-2">office_assistant_rejected</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 d-none d-md-block text-success fs-7">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success h-100 shadow-none">
                                <div class="card-body p-2">
                                    <span class="badge bg-success mb-1">Final Approval</span>
                                    <h6 class="fs-10 fw-bold mb-1">Secretary</h6>
                                    <p class="fs-11 text-600 mb-0">Final approval locks audit history and permanently marks active.</p>
                                    <span class="badge bg-success-subtle text-success mt-2">secretary_approved_final</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning border-2 d-flex align-items-center mb-0" role="alert">
                    <div class="bg-warning me-3 icon-item"><span class="fas fa-exclamation-triangle text-white fs-8"></span></div>
                    <p class="mb-0 fs-10">
                        <strong>Supervisor Audit Trail:</strong> Any rejection generates a real-time notification to the entering operator via BroadcastChannel poller. The operator can edit the rejected application directly from <code>Supervisor &rarr; My Rejected</code>.
                    </p>
                </div>
            </div>

            {{-- CONTRACTOR TAB --}}
            <div class="tab-pane fade" id="tab-contractor" role="tabpanel">
                <h6 class="fw-bold text-danger mb-3">Electrical Contractor License Lifecycle</h6>
                
                <div class="p-3 bg-light rounded-3 mb-4">
                    <div class="row g-2 text-center align-items-center">
                        <div class="col-md-3">
                            <div class="card border-primary h-100 shadow-none">
                                <div class="card-body p-2">
                                    <span class="badge bg-primary mb-1">Company Entry</span>
                                    <h6 class="fs-10 fw-bold mb-1">DEO</h6>
                                    <p class="fs-11 text-600 mb-0">Contractor company name, trade license #, associated supervisor #.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 d-none d-md-block text-primary fs-7">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-info h-100 shadow-none">
                                <div class="card-body p-2">
                                    <span class="badge bg-info mb-1">Verification</span>
                                    <h6 class="fs-10 fw-bold mb-1">Office Assistant</h6>
                                    <p class="fs-11 text-600 mb-0">Verifies trade license validity, company address, and contractor register scan.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 d-none d-md-block text-success fs-7">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success h-100 shadow-none">
                                <div class="card-body p-2">
                                    <span class="badge bg-success mb-1">Archive Lock</span>
                                    <h6 class="fs-10 fw-bold mb-1">Secretary</h6>
                                    <p class="fs-11 text-600 mb-0">Issues final archive authorization with full compound indexing.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-danger border-2 d-flex align-items-center mb-0" role="alert">
                    <div class="bg-danger me-3 icon-item"><span class="fas fa-building text-white fs-8"></span></div>
                    <p class="mb-0 fs-10">
                        <strong>Contractor Entity Constraints:</strong> Contractors require indexed <code>company_name_en</code> and <code>trade_license_number</code>. These indexes allow sub-millisecond filtering across 1,000,000+ historical contractor records.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- SECTION 3: ANNOTATED SAMPLE SCANS & LEDGER EXTRACTION --}}
<div class="card mb-3" id="section-samples">
    <div class="card-header bg-light">
        <h5 class="mb-0 text-900"><i class="fas fa-file-invoice text-success me-2"></i> 3. Annotated Ledger Scans & Data Extraction Guide</h5>
    </div>
    <div class="card-body">
        <p class="fs-10 text-700">Operators must follow these annotations when transcribing physical ledger registers into the digital archive:</p>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card border h-100 shadow-none">
                    <div class="card-header bg-light py-2">
                        <strong class="fs-10 text-primary"><i class="fas fa-search-plus me-1"></i> Sample Ledger: Electrician Register (Annotated)</strong>
                    </div>
                    <div class="card-body p-2 text-center">
                        <img src="{{ asset('assets/img/guide/media_1793421353799_e7be040e.jpg') }}" alt="Electrician Ledger Scan" class="img-fluid rounded border mb-2 shadow-sm" style="max-height: 280px; object-fit: contain;">
                        <div class="text-start fs-11 p-2 bg-light rounded">
                            <div class="mb-1"><span class="badge bg-danger">Annotation [1]</span> <strong>Book Number:</strong> Top right header of the ledger page.</div>
                            <div class="mb-1"><span class="badge bg-primary">Annotation [2]</span> <strong>Old Certificate #:</strong> Column 1 (e.g., <code>EL-XXXXX</code>). Must be exact.</div>
                            <div class="mb-1"><span class="badge bg-success">Annotation [3]</span> <strong>Applicant Name:</strong> Both English and Bengali transcriptions required.</div>
                            <div><span class="badge bg-warning text-dark">Annotation [4]</span> <strong>Class Assigned:</strong> Letters A, B, C, or combinations (ABC).</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border h-100 shadow-none">
                    <div class="card-header bg-light py-2">
                        <strong class="fs-10 text-warning"><i class="fas fa-search-plus me-1"></i> Sample Ledger: Supervisor Register (Annotated)</strong>
                    </div>
                    <div class="card-body p-2 text-center">
                        <img src="{{ asset('assets/img/guide/media_1793421353796_2cdf6736.jpg') }}" alt="Supervisor Ledger Scan" class="img-fluid rounded border mb-2 shadow-sm" style="max-height: 280px; object-fit: contain;">
                        <div class="text-start fs-11 p-2 bg-light rounded">
                            <div class="mb-1"><span class="badge bg-danger">Annotation [A]</span> <strong>Certificate ID:</strong> Supervisor registration tracking code.</div>
                            <div class="mb-1"><span class="badge bg-info">Annotation [B]</span> <strong>Academic Degree:</strong> Diploma/BSc Electrical Engineering details.</div>
                            <div class="mb-1"><span class="badge bg-success">Annotation [C]</span> <strong>Issue & Expiry Dates:</strong> Validated in standard YYYY-MM-DD format.</div>
                            <div><span class="badge bg-secondary">Annotation [D]</span> <strong>Official Seal:</strong> Verification signature of the Board Secretary.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 4: ADVANCED SEARCH & PERFORMANCE INDEXING --}}
<div class="card mb-3" id="section-performance">
    <div class="card-header bg-light">
        <h5 class="mb-0 text-900"><i class="fas fa-tachometer-alt text-primary me-2"></i> 4. Enterprise Performance & Database Optimizations</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <h6 class="fs-10 fw-bold text-dark"><i class="fas fa-database text-primary me-1"></i> Large-Scale Database Indexes</h6>
                <p class="fs-11 text-700 mb-2">To support instant search across millions of records without full-table scans, the v2.0 stack implements dedicated multi-column composite indexes:</p>
                <div class="bg-dark text-light p-3 rounded font-monospace fs-11">
                    -- Compound Filtering Indexes<br>
                    INDEX idx_elec_status_created (status, created_at)<br>
                    INDEX idx_elec_status_updated (status, updated_at)<br>
                    INDEX idx_sup_status_created (status, created_at)<br>
                    INDEX idx_cont_status_created (status, created_at)<br>
                    <br>
                    -- High-Cardinality Field Indexes<br>
                    INDEX idx_elec_book (book_number)<br>
                    INDEX idx_elec_name (applicant_name_en)<br>
                    INDEX idx_cont_company (company_name_en)<br>
                    INDEX idx_cont_trade (trade_license_number)
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="fs-10 fw-bold text-dark"><i class="fas fa-window-restore text-success me-1"></i> Cross-Tab Polling Architecture</h6>
                <p class="fs-11 text-700 mb-2">To prevent server congestion when officers keep multiple tabs open, <code>notification.js</code> uses an automated leader-election pattern:</p>
                <div class="card border p-3 bg-light fs-11">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-primary me-2">Leader Tab</span>
                        <span>Only 1 browser tab issues AJAX requests to <code>/notifications/unread-count</code>.</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-info me-2">BroadcastChannel</span>
                        <span>New updates broadcast instantly across memory bus to all other open tabs.</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2">0% Redundancy</span>
                        <span>Zero duplicated database queries regardless of open tab count.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 5: OPERATIONS & QUICK COMMANDS --}}
<div class="card mb-3" id="section-ops">
    <div class="card-header bg-light">
        <h5 class="mb-0 text-900"><i class="fas fa-terminal text-secondary me-2"></i> 5. System Administration & Deployment Port Map</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered fs-10 mb-3">
                <thead class="table-light">
                    <tr>
                        <th>Service</th>
                        <th>Container Name</th>
                        <th>Host Port</th>
                        <th>Internal Port</th>
                        <th>Default Credentials</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Nginx Web Application</strong></td>
                        <td><code>ocei-v2-nginx</code></td>
                        <td><span class="badge bg-primary">8095</span></td>
                        <td>80</td>
                        <td>Admin: <code>admin@ocei.gov.bd</code> / <code>password</code></td>
                    </tr>
                    <tr>
                        <td><strong>MySQL 9.0 Database</strong></td>
                        <td><code>ocei-v2-db</code></td>
                        <td><span class="badge bg-success">3308</span></td>
                        <td>3306</td>
                        <td>User: <code>ocei_user</code> / <code>OceiArchive@2025</code></td>
                    </tr>
                    <tr>
                        <td><strong>phpMyAdmin GUI</strong></td>
                        <td><code>ocei-v2-phpmyadmin</code></td>
                        <td><span class="badge bg-warning text-dark">8096</span></td>
                        <td>80</td>
                        <td>Root: <code>root</code> / <code>OceiRoot@2025</code></td>
                    </tr>
                    <tr>
                        <td><strong>Node 26 / Vite 8 Dev</strong></td>
                        <td><code>ocei-v2-node</code></td>
                        <td><span class="badge bg-info text-dark">5174</span></td>
                        <td>5174</td>
                        <td>HMR Server</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h6 class="fs-10 fw-bold">Essential CLI Maintenance Commands:</h6>
        <div class="bg-dark text-light p-2 rounded font-monospace fs-11">
            # Start all v2 containers<br>
            docker compose up -d<br><br>
            # Check container health status<br>
            docker compose ps<br><br>
            # Re-compile frontend assets with Tailwind v4 & Vite 8<br>
            docker exec -it ocei-v2-node npm run build<br><br>
            # Clear & optimize application caches<br>
            docker exec -it ocei-v2-app php artisan optimize:clear && docker exec -it ocei-v2-app php artisan config:cache
        </div>
    </div>
</div>
@endsection
