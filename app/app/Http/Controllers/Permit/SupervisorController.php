<?php

namespace App\Http\Controllers\Permit;

use App\Models\SupervisorApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends BasePermitController
{
    public function __construct()
    {
        $this->model = SupervisorApplication::class;
        $this->viewPath = 'permits.supervisor';
        $this->routePrefix = 'permits.supervisor';
        $this->title = 'Supervisor Permit';
    }

    // Override store to handle Supervisor-specific validation
    public function store(Request $request)
    {
        $data = $request->validate([
            'old_certificate_number' => 'required|unique:supervisor_applications,old_certificate_number',
            'applicant_name_en' => 'required',
            'mobile_no' => 'required',
            'engagement_status_with_contractor' => 'nullable|string',
            'contractor_id' => 'nullable|integer',
            'archive_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:3072', // 3MB max
            // Add other fields as "nullable" to allow draft saving
        ]);

        // Standard Draft Fields
        $data = array_merge($request->except(['_token', 'archive_file']), [
            'entry_by' => Auth::id(),
            'entry_at' => now(),
            'application_status' => 'draft'
        ]);

        if ($request->hasFile('archive_file')) {
            $data['archive_file'] = $request->file('archive_file')->store('archives/supervisor', 'public');
        }

        $app = $this->model::create($data);
        $app->logAudit('create', 'Created draft Supervisor application');

        return redirect()->route($this->routePrefix . '.drafts')->with('success', 'Draft saved.');
    }
}
