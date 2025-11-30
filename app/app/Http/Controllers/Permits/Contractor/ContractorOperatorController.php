<?php

namespace App\Http\Controllers\Permits\Contractor;

use App\Http\Controllers\Controller;
use App\Models\ExContractorRenewApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ContractorOperatorController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the operator's records.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ExContractorRenewApplication::class);

        $query = ExContractorRenewApplication::query()
            ->where('entry_by', Auth::id())
            ->with(['attachments', 'entryBy']);

        // Search functionality
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Status filter
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Date range filter
        if ($from = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $perPage = $request->get('per_page', 25);
        $applications = $query->latest()->paginate($perPage)->appends($request->except('page'));

        return view('permits.contractor.operator.index', compact('applications'));
    }

    /**
     * Display pending applications (submitted but not yet approved).
     */
    public function pending(Request $request)
    {
        $this->authorize('viewAny', ExContractorRenewApplication::class);

        $query = ExContractorRenewApplication::query()
            ->where('entry_by', Auth::id())
            ->whereIn('status', ['submitted_to_office_assistant', 'submitted_to_secretary'])
            ->with(['attachments', 'entryBy']);

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        $applications = $query->latest()->paginate(20);

        return view('permits.contractor.operator.pending', compact('applications'));
    }

    /**
     * Display rejected applications.
     */
    public function rejected(Request $request)
    {
        $this->authorize('viewAny', ExContractorRenewApplication::class);

        $query = ExContractorRenewApplication::query()
            ->where('entry_by', Auth::id())
            ->whereIn('status', ['office_assistant_rejected', 'secretary_rejected'])
            ->with(['attachments', 'entryBy', 'rejectedBy']);

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        $applications = $query->latest('updated_at')->paginate(20);

        return view('permits.contractor.operator.rejected', compact('applications'));
    }

    /**
     * Display approved applications (final approved).
     */
    public function approved(Request $request)
    {
        $this->authorize('viewAny', ExContractorRenewApplication::class);

        $query = ExContractorRenewApplication::query()
            ->where('entry_by', Auth::id())
            ->where('status', 'secretary_approved_final')
            ->with(['attachments', 'entryBy', 'approvedBySecretary', 'verifiedByOfficeAssistant']);

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Date filter on approval date
        if ($from = $request->get('date_from')) {
            $query->whereDate('approved_at_secretary', '>=', $from);
        }
        if ($to = $request->get('date_to')) {
            $query->whereDate('approved_at_secretary', '<=', $to);
        }

        $applications = $query->latest('approved_at_secretary')->paginate(20);

        return view('permits.contractor.operator.approved', compact('applications'));
    }

    /**
     * Show the form for creating a new application.
     */
    public function create()
    {
        $this->authorize('create', ExContractorRenewApplication::class);

        return view('permits.contractor.operator.create');
    }

    /**
     * Store a newly created application.
     */
    public function store(Request $request)
    {
        $this->authorize('create', ExContractorRenewApplication::class);

        $validated = $this->validateApplication($request);

        DB::beginTransaction();
        try {
            $application = ExContractorRenewApplication::create([
                ...$validated,
                'status' => 'draft',
                'entry_by' => Auth::id(),
                'entry_at' => now(),
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('ex-contractor.operator.edit', $application)
                ->with('success', 'Application created successfully as draft.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create application: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified application.
     */
    public function show(ExContractorRenewApplication $application)
    {
        $this->authorize('view', $application);

        $application->load(['attachments', 'histories.performedBy', 'entryBy', 'rejectedBy']);

        return view('permits.contractor.operator.show', compact('application'));
    }

    /**
     * Show the form for editing the application.
     */
    public function edit(ExContractorRenewApplication $application)
    {
        $this->authorize('update', $application);

        $application->load('attachments');

        return view('permits.contractor.operator.edit', compact('application'));
    }

    /**
     * Update the specified application.
     */
    public function update(Request $request, ExContractorRenewApplication $application)
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
     * Save and move to next tab.
     */
    public function saveTab(Request $request, ExContractorRenewApplication $application)
    {
        $this->authorize('update', $application);

        $tab = $request->input('current_tab', 1);
        $validated = $this->validateTab($request, $tab);

        DB::beginTransaction();
        try {
            $application->update([
                ...$validated,
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            DB::commit();

            $nextTab = min($tab + 1, 5);

            return redirect()
                ->route('ex-contractor.operator.edit', ['application' => $application, 'tab' => $nextTab])
                ->with('success', 'Progress saved. Moving to next tab.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to save progress: ' . $e->getMessage());
        }
    }

    /**
     * Submit application to office assistant.
     */
    public function submit(ExContractorRenewApplication $application)
    {
        \Log::info('Submit attempt', [
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->admin_type,
            'application_id' => $application->id,
            'application_status' => $application->status,
            'entry_by' => $application->entry_by,
            'attachments_count' => $application->attachments()->count()
        ]);

        try {
            $this->authorize('submitToOfficeAssistant', $application);
        } catch (\Exception $e) {
            \Log::error('Authorization failed', [
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Authorization failed: ' . $e->getMessage());
        }

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

            // Force refresh to ensure latest data
            $application->refresh();

            DB::commit();

            \Log::info('Submit successful', [
                'application_id' => $application->id,
                'new_status' => $application->status
            ]);

            return redirect()
                ->route('ex-contractor.operator.index')
                ->with('success', 'Application submitted successfully. Status: ' . $application->status_label);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to submit application', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to submit application: ' . $e->getMessage());
        }
    }

    /**
     * Bulk submit applications.
     */
    public function bulkSubmit(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:ex_contractor_renew_applications,id',
        ]);

        $applications = ExContractorRenewApplication::whereIn('id', $request->application_ids)
            ->where('entry_by', Auth::id())
            ->get();

        $submitted = [];
        $failed = [];

        foreach ($applications as $application) {
            try {
                if (!$this->authorize('submitToOfficeAssistant', $application)) {
                    $failed[] = [
                        'id' => $application->id,
                        'old_certificate_number' => $application->old_certificate_number,
                        'reason' => 'Not authorized to submit this application.',
                    ];
                    continue;
                }

                if ($application->attachments()->count() === 0) {
                    $failed[] = [
                        'id' => $application->id,
                        'old_certificate_number' => $application->old_certificate_number,
                        'reason' => 'No attachments uploaded.',
                    ];
                    continue;
                }

                DB::beginTransaction();
                $newStatus = ($application->status === 'secretary_rejected')
                    ? 'submitted_to_secretary'
                    : 'submitted_to_office_assistant';

                $application->update([
                    'status' => $newStatus,
                    'last_updated_by' => Auth::id(),
                    'last_updated_at' => now(),
                ]);
                DB::commit();

                $submitted[] = $application->old_certificate_number;
            } catch (\Exception $e) {
                DB::rollBack();
                $failed[] = [
                    'id' => $application->id,
                    'old_certificate_number' => $application->old_certificate_number,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        $message = count($submitted) . ' applications submitted successfully.';
        if (count($failed) > 0) {
            $message .= ' ' . count($failed) . ' applications failed.';
        }

        return back()->with('success', $message)->with('failed_submissions', $failed);
    }

    /**
     * Claim a record by old certificate number.
     */
    public function claim(Request $request)
    {
        $request->validate([
            'old_certificate_number' => 'required|string',
        ]);

        $certificateNumber = $request->old_certificate_number;
        $application = ExContractorRenewApplication::where('old_certificate_number', $certificateNumber)->first();

        // Case 1: Certificate not found - Create new entry
        if (!$application) {
            return redirect()
                ->route('ex-contractor.operator.create', ['old_certificate_number' => $certificateNumber])
                ->with('success', 'Certificate not found. Please create a new application.');
        }

        $isSuperAdmin = Auth::user()->hasRole('super_admin');
        $isOwner = $application->entry_by === Auth::id();

        // Case 2: Final approved - Locked (except for super admin who can view)
        if ($application->status === 'secretary_approved_final') {
            if ($isSuperAdmin) {
                return redirect()
                    ->route('ex-contractor.operator.show', $application)
                    ->with('info', 'This application is FINAL APPROVED and LOCKED. You can view it as Super Admin.');
            }
            return back()->with('error', 'This application is FINAL APPROVED and LOCKED. Cannot be edited.');
        }

        // Case 3: Locked by another user (not current user, not super admin)
        if ($application->entry_by !== null && !$isOwner && !$isSuperAdmin) {
            return back()->with('error', 'This certificate is already claimed by another operator.');
        }

        // Case 4: Owner or unclaimed or super admin - Allow edit
        DB::beginTransaction();
        try {
            // If unclaimed, claim it
            if ($application->entry_by === null) {
                $application->update([
                    'entry_by' => Auth::id(),
                    'entry_at' => now(),
                    'last_updated_by' => Auth::id(),
                    'last_updated_at' => now(),
                ]);
            }

            DB::commit();

            $message = $isOwner
                ? 'Opening your claimed application.'
                : ($isSuperAdmin ? 'Opening as Super Admin.' : 'Record claimed successfully.');

            return redirect()
                ->route('ex-contractor.operator.edit', $application)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process claim: ' . $e->getMessage());
        }
    }

    /**
     * Validate the application data.
     */
    protected function validateApplication(Request $request, ?ExContractorRenewApplication $application = null): array
    {
        $uniqueRule = $application
            ? 'unique:ex_contractor_renew_applications,old_certificate_number,' . $application->id
            : 'unique:ex_contractor_renew_applications,old_certificate_number';

        return $request->validate([
            'old_certificate_number' => 'required|string|max:100|' . $uniqueRule,
            'applicant_name_bn' => 'nullable|string|max:255',
            'applicant_name_en' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date',
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
            'certificate_number' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'renewal_period' => 'nullable|integer',
            'last_renewal_date' => 'nullable|date',
            'result' => 'nullable|string|max:15',
        ]);
    }

    /**
     * Validate specific tab data.
     */
    protected function validateTab(Request $request, int $tab): array
    {
        return match ($tab) {
            1 => $request->validate([
                'old_certificate_number' => 'required|string|max:100',
                'applicant_name_bn' => 'nullable|string|max:255',
                'applicant_name_en' => 'nullable|string|max:255',
                'father_name' => 'nullable|string|max:255',
                'mother_name' => 'nullable|string|max:255',
                'mobile_no' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'date_of_birth' => 'nullable|date',
                'nid_number' => 'nullable|string|max:255',
            ]),
            2 => $request->validate([
                'village' => 'nullable|string',
                'post_office' => 'nullable|string|max:255',
                'postcode' => 'nullable|integer',
                'upazilla' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255',
                'division' => 'nullable|string|max:255',
            ]),
            3 => $request->validate([
                'degree' => 'nullable|string|max:255',
                'subject' => 'nullable|string|max:255',
                'board' => 'nullable|string|max:255',
                'academic_result' => 'nullable|string|max:255',
                'passing_year' => 'nullable|string|max:255',
            ]),
            4 => $request->validate([
                'company' => 'nullable|string|max:255',
                'designation' => 'nullable|string|max:255',
                'total_job_duration' => 'nullable|string|max:255',
            ]),
            5 => $request->validate([
                'certificate_number' => 'nullable|string|max:255',
                'issue_date' => 'nullable|date',
                'expiry_date' => 'nullable|date',
                'renewal_period' => 'nullable|integer',
                'last_renewal_date' => 'nullable|date',
                'result' => 'nullable|string|max:15',
            ]),
            default => [],
        };
    }
}
