<?php

namespace App\Http\Controllers\Permits\Electrician;

use App\Http\Controllers\Controller;
use App\Models\ExElectricianRenewApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ElectricianAdminController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display all applications including soft-deleted.
     */
    public function index(Request $request)
    {
        $this->authorize('superAdminOverride', ExElectricianRenewApplication::class);

        $query = ExElectricianRenewApplication::query();

        // Include soft deleted if requested
        if ($request->get('show_deleted') == '1') {
            $query->onlyTrashed();
        } elseif ($request->get('show_deleted') == 'all') {
            $query->withTrashed();
        }

        // Search functionality
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Status filter
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

        // Statistics
        $statistics = [
            'total' => ExElectricianRenewApplication::withTrashed()->count(),
            'draft' => ExElectricianRenewApplication::where('status', 'draft')->count(),
            'pending' => ExElectricianRenewApplication::whereIn('status', ['submitted_to_office_assistant', 'submitted_to_secretary'])->count(),
            'approved' => ExElectricianRenewApplication::where('status', 'secretary_approved_final')->count(),
            'rejected' => ExElectricianRenewApplication::whereIn('status', ['office_assistant_rejected', 'secretary_rejected'])->count(),
            'deleted' => ExElectricianRenewApplication::onlyTrashed()->count(),
        ];

        return view('permits.electrician.admin.index', compact('applications', 'statistics'));
    }

    /**
     * Show the form for editing (including locked records).
     */
    public function edit(ExElectricianRenewApplication $application)
    {
        $this->authorize('superAdminOverride', ExElectricianRenewApplication::class);

        $application->load('attachments');

        return view('permits.electrician.admin.edit', compact('application'));
    }

    /**
     * Update (with override capability for locked records).
     */
    public function update(Request $request, ExElectricianRenewApplication $application)
    {
        $this->authorize('superAdminOverride', ExElectricianRenewApplication::class);

        $validated = $this->validateApplication($request, $application);

        DB::beginTransaction();
        try {
            $oldValues = $application->toArray();

            $application->update([
                ...$validated,
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            // Log super admin override
            $application->logSuperAdminOverride(
                $validated,
                $request->input('override_reason', 'Super Admin Override')
            );

            DB::commit();

            return back()->with('success', 'Application updated by Super Admin (Override logged).');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update application: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete application.
     */
    public function destroy(ExElectricianRenewApplication $application)
    {
        $this->authorize('delete', $application);

        DB::beginTransaction();
        try {
            $application->update(['deleted_by' => Auth::id()]);
            $application->delete();

            DB::commit();

            return redirect()
                ->route('ex-electrician.admin.index')
                ->with('success', 'Application soft deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete application: ' . $e->getMessage());
        }
    }

    /**
     * Restore soft deleted application.
     */
    public function restore($id)
    {
        $application = ExElectricianRenewApplication::withTrashed()->findOrFail($id);

        $this->authorize('restore', $application);

        DB::beginTransaction();
        try {
            $application->restore();
            $application->update(['deleted_by' => null]);

            DB::commit();

            return redirect()
                ->route('ex-electrician.admin.index')
                ->with('success', 'Application restored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to restore application: ' . $e->getMessage());
        }
    }

    /**
     * Change status (admin override).
     */
    public function changeStatus(Request $request, ExElectricianRenewApplication $application)
    {
        $this->authorize('superAdminOverride', ExElectricianRenewApplication::class);

        $request->validate([
            'status' => 'required|string|in:draft,submitted_to_office_assistant,office_assistant_rejected,submitted_to_secretary,secretary_rejected,secretary_approved_final',
            'reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $application->status;

            $application->update([
                'status' => $request->status,
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            $application->logSuperAdminOverride(
                ['status' => $request->status],
                'Status changed from "' . $oldStatus . '" to "' . $request->status . '". Reason: ' . $request->reason
            );

            DB::commit();

            return back()->with('success', 'Status changed successfully (Override logged).');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to change status: ' . $e->getMessage());
        }
    }

    /**
     * Validate the application data.
     */
    protected function validateApplication(Request $request, ExElectricianRenewApplication $application): array
    {
        return $request->validate([
            'old_certificate_number' => 'required|string|max:100|unique:ex_electrician_renew_applications,old_certificate_number,' . $application->id,
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
}
