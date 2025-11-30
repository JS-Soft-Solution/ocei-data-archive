<?php

namespace App\Http\Controllers\Permits\Electrician;

use App\Http\Controllers\Controller;
use App\Models\ExElectricianRenewApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ElectricianOfficeAssistantController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display pending applications for office assistant review.
     */
    public function pending(Request $request)
    {
        $query = ExElectricianRenewApplication::query()
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

        $applications = $query->latest()->paginate(20);

        return view('permits.electrician.office-assistant.pending', compact('applications'));
    }

    /**
     * Display applications rejected by this office assistant (not yet resubmitted).
     */
    public function rejected(Request $request)
    {
        $query = ExElectricianRenewApplication::query()
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

        $applications = $query->latest('rejected_at')->paginate(20);

        return view('permits.electrician.office-assistant.rejected', compact('applications'));
    }

    /**
     * Display applications approved by this office assistant.
     */
    public function approved(Request $request)
    {
        $query = ExElectricianRenewApplication::query()
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

        $applications = $query->latest('verified_at_office_assistant')->paginate(20);

        return view('permits.electrician.office-assistant.approved', compact('applications'));
    }

    /**
     * Display the specified application for review.
     */
    public function show(ExElectricianRenewApplication $application)
    {
        $this->authorize('view', $application);

        $application->load(['attachments', 'histories.performedBy', 'entryBy']);

        return view('permits.electrician.office-assistant.show', compact('application'));
    }

    /**
     * Approve and forward to secretary.
     */
    public function approve(ExElectricianRenewApplication $application)
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

            return redirect()
                ->route('ex-electrician.office-assistant.pending')
                ->with('success', 'Application approved and forwarded to Secretary.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve application: ' . $e->getMessage());
        }
    }

    /**
     * Reject application.
     */
    public function reject(Request $request, ExElectricianRenewApplication $application)
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

            return redirect()
                ->route('ex-electrician.office-assistant.pending')
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
            'application_ids.*' => 'exists:ex_electrician_renew_applications,id',
        ]);

        $applications = ExElectricianRenewApplication::whereIn('id', $request->application_ids)
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
            'application_ids.*' => 'exists:ex_electrician_renew_applications,id',
            'reject_reason' => 'required|string|max:1000',
        ]);

        $applications = ExElectricianRenewApplication::whereIn('id', $request->application_ids)
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
