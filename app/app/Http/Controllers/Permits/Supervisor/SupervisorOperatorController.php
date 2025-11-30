<?php

namespace App\Http\Controllers\Permits\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\ExSupervisorRenewApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SupervisorOperatorController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the operator's records.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ExSupervisorRenewApplication::class);

        $query = ExSupervisorRenewApplication::query()
            ->where('entry_by', Auth::id())
            ->with(['attachments', 'entryBy']);

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $applications = $query->latest()->paginate(20);

        return view('permits.supervisor.operator.index', compact('applications'));
    }

    /**
     * Show the form for creating a new application.
     */
    public function create()
    {
        $this->authorize('create', ExSupervisorRenewApplication::class);

        return view('permits.supervisor.operator.create');
    }

    /**
     * Store a newly created application.
     */
    public function store(Request $request)
    {
        $this->authorize('create', ExSupervisorRenewApplication::class);

        $validated = $this->validateApplication($request);

        DB::beginTransaction();
        try {
            $application = ExSupervisorRenewApplication::create([
                ...$validated,
                'status' => 'draft',
                'entry_by' => Auth::id(),
                'entry_at' => now(),
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('ex-supervisor.operator.edit', $application)
                ->with('success', 'Application created successfully as draft.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create application: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the application.
     */
    public function edit(ExSupervisorRenewApplication $application)
    {
        $this->authorize('update', $application);

        $application->load('attachments');

        return view('permits.supervisor.operator.edit', compact('application'));
    }

    /**
     * Update the specified application.
     */
    public function update(Request $request, ExSupervisorRenewApplication $application)
    {
        $this->authorize('update', $application);

        $validated = $this->validateApplication($request, $application);

        DB::beginTransaction();
        try {
            $application->update([
                ...$validated,
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Application updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update application: ' . $e->getMessage());
        }
    }

    /**
     * Submit application.
     */
    public function submit(ExSupervisorRenewApplication $application)
    {
        $this->authorize('submitToOfficeAssistant', $application);

        if ($application->attachments()->count() === 0) {
            return back()->with('error', 'Please upload at least one attachment before submitting.');
        }

        DB::beginTransaction();
        try {
            $newStatus = ($application->status === 'secretary_rejected')
                ? 'submitted_to_secretary'
                : 'submitted_to_office_assistant';

            $application->update([
                'status' => $newStatus,
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('ex-supervisor.operator.index')
                ->with('success', 'Application submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit application: ' . $e->getMessage());
        }
    }

    /**
     * Validate the application data.
     */
    protected function validateApplication(Request $request, ?ExSupervisorRenewApplication $application = null): array
    {
        $uniqueRule = $application
            ? 'unique:ex_supervisor_renew_applications,old_certificate_number,' . $application->id
            : 'unique:ex_supervisor_renew_applications,old_certificate_number';

        return $request->validate([
            'old_certificate_number' => 'required|string|max:100|' . $uniqueRule,
            'applicant_name_bn' => 'nullable|string|max:255',
            'applicant_name_en' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|string|max:255',
            'nid_number' => 'nullable|string|max:255',
            'village' => 'nullable|string',
            'post_office' => 'nullable|string|max:255',
            'postcode' => 'nullable|integer',
            'upazilla' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'division' => 'nullable|string|max:255',
            'degree' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'board' => 'nullable|string|max:255',
            'academic_result' => 'nullable|string|max:255',
            'passing_year' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'total_job_duration' => 'nullable|string|max:255',
            'engagement_status_with_contractor' => 'nullable|string|max:20',
            'contractor_id' => 'nullable|integer',
            'certificate_number' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'renewal_period' => 'nullable|integer',
            'last_renewal_date' => 'nullable|date',
            'result' => 'nullable|string|max:15',
        ]);
    }
}
