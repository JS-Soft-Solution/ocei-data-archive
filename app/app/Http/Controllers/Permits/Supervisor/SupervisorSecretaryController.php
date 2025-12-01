<?php

namespace App\Http\Controllers\Permits\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\ExSupervisorRenewApplication;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SupervisorSecretaryController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display pending applications for secretary final approval.
     */
    public function pending(Request $request)
    {
        $query = ExSupervisorRenewApplication::query()
            ->where('status', 'submitted_to_secretary')
            ->with(['attachments', 'entryBy', 'verifiedByOfficeAssistant']);

        // Search functionality
        if ($search = $request->get('search')) {
            $query->search($search);
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

        return view('permits.supervisor.secretary.pending', compact('applications'));
    }

    /**
     * Display applications rejected by this secretary (not yet resubmitted).
     */
    public function rejected(Request $request)
    {
        $query = ExSupervisorRenewApplication::query()
            ->where('status', 'secretary_rejected')
            ->where('rejected_by', Auth::id())
            ->with(['attachments', 'entryBy', 'verifiedByOfficeAssistant']);

        // Search functionality
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Date range filter
        if ($from = $request->get('date_from')) {
            $query->whereDate('rejected_at', '>=', $from);
        }
        if ($to = $request->get('date_to')) {
            $query->whereDate('rejected_at', '<=', $to);
        }

        $applications = $query->latest('rejected_at')$perPage = request()->get("per_page", 25);
        $applications = $query->latest()->paginate($perPage)->appends(request()->except("page"));

        return view('permits.supervisor.secretary.rejected', compact('applications'));
    }

    /**
     * Display applications approved by this secretary (FINAL APPROVED).
     */
    public function approved(Request $request)
    {
        $query = ExSupervisorRenewApplication::query()
            ->where('status', 'secretary_approved_final')
            ->where('approved_by_secretary', Auth::id())
            ->with(['attachments', 'entryBy', 'verifiedByOfficeAssistant']);

        // Search functionality
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Date range filter
        if ($from = $request->get('date_from')) {
            $query->whereDate('approved_at_secretary', '>=', $from);
        }
        if ($to = $request->get('date_to')) {
            $query->whereDate('approved_at_secretary', '<=', $to);
        }

        $applications = $query->latest('approved_at_secretary')$perPage = request()->get("per_page", 25);
        $applications = $query->latest()->paginate($perPage)->appends(request()->except("page"));

        return view('permits.supervisor.secretary.approved', compact('applications'));
    }

    /**
     * Display the specified application for final review.
     */
    public function show(ExSupervisorRenewApplication $application)
    {
        $this->authorize('view', $application);

        $application->load(['attachments', 'histories.performedBy', 'entryBy', 'verifiedByOfficeAssistant']);

        return view('permits.supervisor.secretary.show', compact('application'));
    }

    /**
     * Final approve (LOCKS the record).
     */
    public function approve(ExSupervisorRenewApplication $application)
    {
        $this->authorize('approveAsSecretary', $application);

        DB::beginTransaction();
        try {
            $application->update([
                'status' => 'secretary_approved_final',
                'approved_by_secretary' => Auth::id(),
                'approved_at_secretary' => now(),
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            DB::commit();

            // Create notification for data entry operator
            NotificationService::notifyApplicationApproved($application, 'supervisor', 'Secretary');

            return redirect()
                ->route('ex-supervisor.secretary.pending')
                ->with('success', 'Application APPROVED and LOCKED. No further edits allowed except by Super Admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve application: ' . $e->getMessage());
        }
    }

    /**
     * Reject application (sends back to operator, skips OA).
     */
    public function reject(Request $request, ExSupervisorRenewApplication $application)
    {
        $this->authorize('rejectAsSecretary', $application);

        $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $application->update([
                'status' => 'secretary_rejected',
                'rejected_by' => Auth::id(),
                'reject_reason' => $request->reject_reason,
                'rejected_at' => now(),
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            DB::commit();

            // Create notification for data entry operator
            NotificationService::notifyApplicationRejected($application, 'supervisor', 'Secretary', $request->reject_reason);

            return redirect()
                ->route('ex-supervisor.secretary.pending')
                ->with('success', 'Application rejected. Operator can resubmit directly to Secretary.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject application: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve applications.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:ex_supervisor_renew_applications,id',
        ]);

        $applications = ExSupervisorRenewApplication::whereIn('id', $request->application_ids)
            ->where('status', 'submitted_to_secretary')
            ->get();

        $approved = 0;
        $failed = [];

        foreach ($applications as $application) {
            try {
                DB::beginTransaction();
                $application->update([
                    'status' => 'secretary_approved_final',
                    'approved_by_secretary' => Auth::id(),
                    'approved_at_secretary' => now(),
                    'last_updated_by' => Auth::id(),
                    'last_updated_at' => now(),
                ]);
                DB::commit();
                $approved++;
            } catch (\Exception $e) {
                DB::rollBack();
                $failed[] = [
                    'id' => $application->id,
                    'old_certificate_number' => $application->old_certificate_number,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        $message = $approved . ' applications APPROVED and LOCKED.';
        if (count($failed) > 0) {
            $message .= ' ' . count($failed) . ' applications failed.';
        }

        return back()->with('success', $message)->with('failed_approvals', $failed);
    }

    /**
     * Bulk reject applications.
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:ex_supervisor_renew_applications,id',
            'reject_reason' => 'required|string|max:1000',
        ]);

        $applications = ExSupervisorRenewApplication::whereIn('id', $request->application_ids)
            ->where('status', 'submitted_to_secretary')
            ->get();

        $rejected = 0;
        $failed = [];

        foreach ($applications as $application) {
            try {
                DB::beginTransaction();
                $application->update([
                    'status' => 'secretary_rejected',
                    'rejected_by' => Auth::id(),
                    'reject_reason' => $request->reject_reason,
                    'rejected_at' => now(),
                    'last_updated_by' => Auth::id(),
                    'last_updated_at' => now(),
                ]);
                DB::commit();
                $rejected++;
            } catch (\Exception $e) {
                DB::rollBack();
                $failed[] = [
                    'id' => $application->id,
                    'old_certificate_number' => $application->old_certificate_number,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        $message = $rejected . ' applications rejected successfully.';
        if (count($failed) > 0) {
            $message .= ' ' . count($failed) . ' applications failed.';
        }

        return back()->with('success', $message)->with('failed_rejections', $failed);
    }
}
