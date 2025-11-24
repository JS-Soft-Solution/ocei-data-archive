<?php

namespace App\Http\Controllers\Permit;

use App\Models\ContractorApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractorController extends BasePermitController
{
    public function __construct()
    {
        $this->model = ContractorApplication::class;
        $this->viewPath = 'permits.contractor';
        $this->routePrefix = 'permits.contractor';
        $this->title = 'Contractor Permit';
    }

    public function store(Request $request)
    {
        $request->validate([
            'old_certificate_number' => 'required|unique:contractor_applications,old_certificate_number',
            'company_name_en' => 'required',
            'archive_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max for contractors
        ]);

        $data = array_merge($request->except(['_token', 'archive_file']), [
            'entry_by' => Auth::id(),
            'entry_at' => now(),
            'application_status' => 'draft'
        ]);

        if ($request->hasFile('archive_file')) {
            $data['archive_file'] = $request->file('archive_file')->store('archives/contractor', 'public');
        }

        $app = $this->model::create($data);
        $app->logAudit('create', 'Created draft Contractor application');

        return redirect()->route($this->routePrefix . '.drafts')->with('success', 'Draft saved.');
    }
}
