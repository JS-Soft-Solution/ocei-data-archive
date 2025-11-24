<?php

namespace App\Http\Controllers\Permit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
// You'll need an Export class for Excel

abstract class BasePermitController extends Controller
{
    protected $model;
    protected $viewPath;
    protected $routePrefix;
    protected $title;

    // --- OPERATOR ACTIONS ---

    public function search(Request $request)
    {
        if ($request->has('old_cert_no')) {
            $certNo = $request->input('old_cert_no');
            $application = $this->model::where('old_certificate_number', $certNo)->first();

            if ($application) {
                // If verified/approved/submitted, operator can't touch unless rejected to them
                if (in_array($application->application_status, ['submitted', 'verified', 'approved'])) {
                    return back()->with('error', 'Application is currently locked in workflow status: ' . $application->application_status);
                }
                // If claimed by another operator
                if ($application->entry_by && $application->entry_by != Auth::id()) {
                    return back()->with('error', 'This certificate is being processed by another operator.');
                }
                return redirect()->route($this->routePrefix . '.edit', $application->id);
            } else {
                // New entry
                return redirect()->route($this->routePrefix . '.create', ['old_cert_no' => $certNo]);
            }
        }
        return view($this->viewPath . '.search', ['title' => $this->title]);
    }

    public function create(Request $request)
    {
        return view($this->viewPath . '.create', [
            'oldCertNo' => $request->input('old_cert_no'),
            'title' => $this->title
        ]);
    }

    public function store(Request $request)
    {
        // Validation should be in child controller or Request class
        $data = $request->except(['_token', 'archive_file']);
        $data['entry_by'] = Auth::id();
        $data['entry_at'] = now();
        $data['application_status'] = 'draft';

        if ($request->hasFile('archive_file')) {
            $data['archive_file'] = $request->file('archive_file')->store('archives', 'public');
        }

        $app = $this->model::create($data);
        $app->logAudit('create', 'Created draft application');

        return redirect()->route($this->routePrefix . '.drafts')->with('success', 'Draft saved.');
    }

    public function update(Request $request, $id)
    {
        $application = $this->model::findOrFail($id);
        $data = $request->except(['_token', '_method', 'archive_file']);
        $data['last_update_by'] = Auth::id();

        if ($request->hasFile('archive_file')) {
            // Delete old
            if($application->archive_file) Storage::disk('public')->delete($application->archive_file);
            $data['archive_file'] = $request->file('archive_file')->store('archives', 'public');
        }

        // Reset rejection status if re-submitting
        if ($application->application_status == 'rejected') {
            $data['application_status'] = 'draft';
            $data['rejected_by'] = null;
            $data['reject_reason'] = null;
        }

        $application->update($data);
        $application->logAudit('update', 'Updated application data');

        return back()->with('success', 'Updated successfully.');
    }

    public function submitToOffice($id)
    {
        $app = $this->model::findOrFail($id);
        $app->update(['application_status' => 'submitted']);
        $app->logAudit('submit', 'Submitted for verification');
        return back()->with('success', 'Submitted to Office Assistant.');
    }

    // --- LIST VIEWS (OPERATOR) ---
    public function drafts() {
        $apps = $this->model::where('entry_by', Auth::id())->where('application_status', 'draft')->paginate(20);
        return view($this->viewPath . '.index', ['applications' => $apps, 'title' => 'My Drafts', 'routePrefix' => $this->routePrefix]);
    }
    public function rejected() {
        $apps = $this->model::where('entry_by', Auth::id())->where('application_status', 'rejected')->paginate(20);
        return view($this->viewPath . '.index', ['applications' => $apps, 'title' => 'Rejected/Returned', 'routePrefix' => $this->routePrefix]);
    }
    public function submitted() {
        $apps = $this->model::where('entry_by', Auth::id())->where('application_status', 'submitted')->paginate(20);
        return view($this->viewPath . '.index', ['applications' => $apps, 'title' => 'Submitted', 'routePrefix' => $this->routePrefix]);
    }
    public function approvedListOperator() {
        $apps = $this->model::where('entry_by', Auth::id())->where('application_status', 'approved')->paginate(20);
        return view($this->viewPath . '.index', ['applications' => $apps, 'title' => 'Approved', 'routePrefix' => $this->routePrefix]);
    }

    // --- OFFICE ASSISTANT ---
    public function pendingForAssistant() {
        $apps = $this->model::where('application_status', 'submitted')->paginate(20);
        return view($this->viewPath . '.index', ['applications' => $apps, 'title' => 'Pending Verification', 'routePrefix' => $this->routePrefix]);
    }
    public function verify($id) {
        $app = $this->model::findOrFail($id);
        $app->update(['application_status' => 'verified', 'verified_by' => Auth::id(), 'verified_at' => now()]);
        $app->logAudit('verify', 'Verified by Office Assistant');
        return back()->with('success', 'Verified & Forwarded to Secretary.');
    }
    public function reject(Request $request, $id) {
        $app = $this->model::findOrFail($id);
        $app->update(['application_status' => 'rejected', 'rejected_by' => Auth::id(), 'rejected_at' => now(), 'reject_reason' => $request->reason]);
        $app->logAudit('reject', 'Rejected: ' . $request->reason);
        return back()->with('success', 'Application Rejected.');
    }
    public function approvedByAssistant() {
        $apps = $this->model::where('verified_by', Auth::id())->where('application_status', 'verified')->paginate(20);
        return view($this->viewPath . '.index', ['applications' => $apps, 'title' => 'Verified by Me (Pending Final)', 'routePrefix' => $this->routePrefix]);
    }

    // --- SECRETARY ---
    public function pendingForSecretary() {
        $apps = $this->model::where('application_status', 'verified')->paginate(20);
        return view($this->viewPath . '.index', ['applications' => $apps, 'title' => 'Pending Final Approval', 'routePrefix' => $this->routePrefix]);
    }
    public function approve($id) {
        $app = $this->model::findOrFail($id);
        $app->update(['application_status' => 'approved', 'approved_by' => Auth::id(), 'approved_at' => now()]);
        $app->logAudit('approve', 'Final Approval');
        return back()->with('success', 'Application Approved.');
    }
    public function approvedBySecretary() {
        $apps = $this->model::where('application_status', 'approved')->orderBy('approved_at', 'desc')->paginate(20);
        return view($this->viewPath . '.index', ['applications' => $apps, 'title' => 'All Approved Applications', 'routePrefix' => $this->routePrefix]);
    }

    // --- REPORTING / EXPORT ---
    public function export($type) {
        // Simple logic: if PDF, use DomPDF view. If Excel, use Maatwebsite export class.
        // Implementation depends on specific library choice.
        // Example PDF:
        // $data = $this->model::all();
        // $pdf = Pdf::loadView($this->viewPath . '.pdf_report', compact('data'));
        // return $pdf->download('report.pdf');
    }
}
