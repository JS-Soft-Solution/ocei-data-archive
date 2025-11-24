<?php

namespace App\Http\Controllers\Permit;

use App\Http\Controllers\Controller;
use App\Models\ElectricianApplication;
use App\Models\AuditLog; // Assuming you created this model previously
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; // Requires barryvdh/laravel-dompdf package

class ElectricianController extends Controller
{
    // --- 1. SEARCH & CREATE (Operator) ---

    public function search(Request $request)
    {
        if ($request->has('old_cert_no')) {
            $certNo = $request->input('old_cert_no');

            // Find existing application
            $application = ElectricianApplication::where('old_certificate_number', $certNo)->first();

            if ($application) {
                // Check ownership: If already claimed by another operator, block access
                if ($application->entry_by && $application->entry_by != Auth::id() && !Auth::user()->isSuperAdmin()) {
                    return back()->with('error', 'This certificate is currently being processed by another operator.');
                }

                // If status is locked (submitted/verified/approved), prevent editing unless rejected
                if (in_array($application->application_status, ['submitted', 'verified', 'approved'])) {
                    return back()->with('error', 'Application is currently in ' . $application->application_status . ' status and cannot be edited.');
                }

                // Redirect to Edit
                return redirect()->route('permits.electrician.edit', $application->id);
            } else {
                // Redirect to Create New
                return redirect()->route('permits.electrician.create', ['old_cert_no' => $certNo]);
            }
        }
        return view('permits.electrician.search');
    }

    public function create(Request $request)
    {
        return view('permits.electrician.form', [
            'oldCertNo' => $request->input('old_cert_no'),
            'mode' => 'create',
            'application' => null
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'old_certificate_number' => 'required|unique:electrician_applications,old_certificate_number',
            'applicant_name_en' => 'required|string',
            'applicant_name_bn' => 'nullable|string',
            'father_name' => 'nullable|string',
            'mobile_no' => 'required|string',
            'email' => 'nullable|email',
            'nid_number' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'archive_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:3072', // 3MB Max
            // Add all other fields here...
        ]);

        // Auto-fill tracking fields
        $data['entry_by'] = Auth::id();
        $data['entry_at'] = now();
        $data['application_status'] = 'draft';

        if ($request->hasFile('archive_file')) {
            $data['archive_file'] = $request->file('archive_file')->store('permits/electrician', 'public');
        }

        $app = ElectricianApplication::create($data);

        $this->logAudit($app, 'create', 'Created draft application');

        return redirect()->route('permits.electrician.drafts')->with('success', 'Draft saved successfully.');
    }

    // --- 2. EDIT & UPDATE (Operator) ---

    public function edit($id)
    {
        $application = ElectricianApplication::findOrFail($id);

        // Authorization check (Simple version)
        if ($application->entry_by != Auth::id() && !Auth::user()->hasRole(['super_admin', 'office_assistant', 'secretary'])) {
            abort(403, 'Unauthorized access to this application.');
        }

        return view('permits.electrician.form', [
            'application' => $application,
            'mode' => 'edit'
        ]);
    }

    public function update(Request $request, $id)
    {
        $application = ElectricianApplication::findOrFail($id);

        $data = $request->validate([
            'applicant_name_en' => 'required|string',
            'mobile_no' => 'required|string',
            'archive_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
            // Add validation for other editable fields
        ]);

        $data['last_update_by'] = Auth::id();

        if ($request->hasFile('archive_file')) {
            if ($application->archive_file) {
                Storage::disk('public')->delete($application->archive_file);
            }
            $data['archive_file'] = $request->file('archive_file')->store('permits/electrician', 'public');
        }

        // If application was rejected, reset it to draft so it can be resubmitted
        if ($application->application_status === 'rejected') {
            $data['application_status'] = 'draft';
            $data['rejected_by'] = null;
            $data['reject_reason'] = null;
        }

        $application->update($data);
        $this->logAudit($application, 'update', 'Updated application details');

        return back()->with('success', 'Application updated successfully.');
    }

    // --- 3. WORKFLOW ACTIONS (Submit, Verify, Approve, Reject) ---

    public function submitToOffice($id)
    {
        $app = ElectricianApplication::findOrFail($id);
        $app->update(['application_status' => 'submitted']);
        $this->logAudit($app, 'submit', 'Submitted to Office Assistant');
        return back()->with('success', 'Application submitted for verification.');
    }

    public function verify(Request $request, $id)
    {
        // Office Assistant Action
        $app = ElectricianApplication::findOrFail($id);
        $app->update([
            'application_status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now()
        ]);
        $this->logAudit($app, 'verify', 'Verified by Office Assistant');
        return back()->with('success', 'Application verified and forwarded to Secretary.');
    }

    public function approve(Request $request, $id)
    {
        // Secretary Action
        $app = ElectricianApplication::findOrFail($id);
        $app->update([
            'application_status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'renewal_status' => 'completed' // Or whatever logic you have for renewal
        ]);
        $this->logAudit($app, 'approve', 'Final Approval by Secretary');
        return back()->with('success', 'Application Approved Successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $app = ElectricianApplication::findOrFail($id);
        $app->update([
            'application_status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'reject_reason' => $request->input('reason')
        ]);

        $this->logAudit($app, 'reject', 'Rejected: ' . $request->input('reason'));
        return back()->with('success', 'Application rejected and returned to operator.');
    }

    // --- 4. LISTING & FILTERING ---

    /**
     * Generic index method used by all roles, filtered by the route logic.
     */
    public function index(Request $request)
    {
        $query = ElectricianApplication::query()->with(['entryUser', 'verifiedUser']);
        $title = 'Electrician Applications';

        // --- Role-Based Scopes (Simulated logic usually handled by route definition) ---

        // 1. Operator: Drafts
        if ($request->routeIs('permits.electrician.drafts')) {
            $query->where('entry_by', Auth::id())->where('application_status', 'draft');
            $title = 'My Draft Applications';
        }
        // 2. Operator: Rejected
        elseif ($request->routeIs('permits.electrician.rejected')) {
            $query->where('entry_by', Auth::id())->where('application_status', 'rejected');
            $title = 'Returned / Rejected Applications';
        }
        // 3. Office: Pending Verification
        elseif ($request->routeIs('permits.electrician.pending')) {
            $query->where('application_status', 'submitted');
            $title = 'Pending Verification (Office)';
        }
        // 4. Secretary: Pending Approval
        elseif ($request->routeIs('permits.electrician.secretary.pending')) {
            $query->where('application_status', 'verified');
            $title = 'Pending Final Approval';
        }
        // 5. Approved (Everyone - specific view logic handles columns)
        elseif (str_contains($request->route()->getName(), 'approved')) {
            $query->where('application_status', 'approved');
            $title = 'Approved Electrician Permits';
        }

        // --- Filters ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('old_certificate_number', 'like', "%$search%")
                    ->orWhere('mobile_no', 'like', "%$search%")
                    ->orWhere('applicant_name_en', 'like', "%$search%");
            });
        }

        // --- Export Logic ---
        if ($request->has('export') && $request->export == 'pdf') {
            $data = $query->get(); // Get all data for PDF
            $pdf = Pdf::loadView('permits.electrician.pdf_report', compact('data', 'title'));
            return $pdf->download('electrician_permits.pdf');
        }

        $applications = $query->latest()->paginate(20);

        return view('permits.electrician.index', compact('applications', 'title'));
    }

    // --- HELPER: Audit Log ---
    private function logAudit($model, $action, $comments = null)
    {
        // Ensure you have an AuditLog model
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'comments' => $comments,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
