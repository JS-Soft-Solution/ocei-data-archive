<?php

namespace App\Http\Controllers\Permits\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\ExSupervisorRenewApplication;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SupervisorOfficeAssistantController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display pending applications for office assistant review.
     */
    public function pending(Request $request)
    {
        $query = ExSupervisorRenewApplication::query()
            ->where('status', 'submitted_to_office_assistant')
            ->with(['attachments', 'entryBy']);

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

        return view('permits.supervisor.office-assistant.pending', compact('applications'));
    }

    /**
     * Display applications rejected by this office assistant (not yet resubmitted).
     */
    public function rejected(Request $request)
    {
        $query = ExSupervisorRenewApplication::query()
            ->where('status', 'office_assistant_rejected')
            ->where('rejected_by', Auth::id())
            ->with(['attachments', 'entryBy']);

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

        $perPage = $request->get('per_page', 25);
        $applications = $query->latest('rejected_at')->paginate($perPage)->appends($request->except('page'));

        return view('permits.supervisor.office-assistant.rejected', compact('applications'));
    }

    /**
     * Display applications approved by this office assistant.
     */
    public function approved(Request $request)
    {
        $query = ExSupervisorRenewApplication::query()
            ->where('verified_by_office_assistant', Auth::id())
            ->with(['attachments', 'entryBy'])
            ->whereIn('status', ['submitted_to_secretary', 'secretary_approved_final', 'secretary_rejected']);

        // Search functionality
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Date range filter
        if ($from = $request->get('date_from')) {
            $query->whereDate('verified_at_office_assistant', '>=', $from);
        }
        if ($to = $request->get('date_to')) {
            $query->whereDate('verified_at_office_assistant', '<=', $to);
        }

        $perPage = $request->get('per_page', 25);
        $applications = $query->latest('verified_at_office_assistant')->paginate($perPage)->appends($request->except('page'));

        return view('permits.supervisor.office-assistant.approved', compact('applications'));
    }

    /**
     * Display the specified application for review.
     */
    public function show(ExSupervisorRenewApplication $application)
    {
        $this->authorize('view', $application);

        $application->load(['attachments', 'histories.performedBy', 'entryBy']);

        return view('permits.supervisor.office-assistant.show', compact('application'));
    }

    /**
     * Approve and forward to secretary.
     */
    public function approve(ExSupervisorRenewApplication $application)
    {
        $this->authorize('verifyAsOfficeAssistant', $application);

        DB::beginTransaction();
        try {
            $application->update([
                'status' => 'submitted_to_secretary',
                'verified_by_office_assistant' => Auth::id(),
                'verified_at_office_assistant' => now(),
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            DB::commit();

            // Create notifications
            NotificationService::notifyApplicationForwardedToSecretary($application, 'supervisor');
            NotificationService::notifyApplicationApproved($application, 'supervisor', 'Office Assistant');

            return redirect()
                ->route('ex-supervisor.office-assistant.pending')
                ->with('success', 'Application approved and forwarded to Secretary.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve application: ' . $e->getMessage());
        }
    }

    /**
     * Reject application.
     */
    public function reject(Request $request, ExSupervisorRenewApplication $application)
    {
        $this->authorize('rejectAsOfficeAssistant', $application);

        $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $application->update([
                'status' => 'office_assistant_rejected',
                'rejected_by' => Auth::id(),
                'reject_reason' => $request->reject_reason,
                'rejected_at' => now(),
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            DB::commit();

            // Create notification for data entry operator
            NotificationService::notifyApplicationRejected($application, 'supervisor', 'Office Assistant', $request->reject_reason);

            return redirect()
                ->route('ex-supervisor.office-assistant.pending')
                ->with('success', 'Application rejected successfully.');
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
            ->where('status', 'submitted_to_office_assistant')
            ->get();

        $approved = 0;
        $failed = [];

        foreach ($applications as $application) {
            try {
                DB::beginTransaction();
                $application->update([
                    'status' => 'submitted_to_secretary',
                    'verified_by_office_assistant' => Auth::id(),
                    'verified_at_office_assistant' => now(),
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

        $message = $approved . ' applications approved successfully.';
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
            ->where('status', 'submitted_to_office_assistant')
            ->get();

        $rejected = 0;
        $failed = [];

        foreach ($applications as $application) {
            try {
                DB::beginTransaction();
                $application->update([
                    'status' => 'office_assistant_rejected',
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
