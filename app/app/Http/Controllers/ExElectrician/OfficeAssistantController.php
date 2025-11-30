<?php
// app/Http/Controllers/ExElectrician/OfficeAssistantController.php

namespace App\Http\Controllers\ExElectrician;

use App\Http\Controllers\Controller;
use App\Models\ExElectricianRenewApplication;
use Illuminate\Http\Request;

class OfficeAssistantController extends Controller
{
    public function pending(Request $request)
    {
        $query = ExElectricianRenewApplication::with(['entryBy', 'attachments'])
            ->pendingForOfficeAssistant();

        // Filters: search, dates, etc.
        $query->when($request->search, fn($q) => $q->where('old_certificate_number', 'like', '%'.$request->search.'%'));

        return view('ex-electrician.office-assistant.pending', ['applications' => $query->paginate(20)]);
    }

    public function approve(ExElectricianRenewApplication $record)
    {
        $this->authorize('approveAsOfficeAssistant', $record);
        $record->update([
            'status' => 'submitted_to_secretary',
            'verified_by_office_assistant' => Auth::id(),
            'verified_at_office_assistant' => now(),
        ]);

        return redirect()->back()->with('success', 'Approved');
    }

    public function reject(Request $request, ExElectricianRenewApplication $record)
    {
        $this->authorize('rejectAsOfficeAssistant', $record);
        $request->validate(['reject_reason' => 'required|string|max:1000']);

        $record->update([
            'status' => 'office_assistant_rejected',
            'rejected_by' => Auth::id(),
            'reject_reason' => $request->reject_reason,
        ]);

        return redirect()->back()->with('success', 'Rejected');
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->input('selected', []);
        foreach ($ids as $id) {
            $record = ExElectricianRenewApplication::findOrFail($id);
            $this->authorize('approveAsOfficeAssistant', $record);
            $record->update([
                'status' => 'submitted_to_secretary',
                'verified_by_office_assistant' => Auth::id(),
                'verified_at_office_assistant' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Bulk approved');
    }

    public function bulkReject(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
            'reject_reason' => 'required|string|max:1000', // Common reason for bulk
        ]);

        $ids = $request->input('selected');
        foreach ($ids as $id) {
            $record = ExElectricianRenewApplication::findOrFail($id);
            $this->authorize('rejectAsOfficeAssistant', $record);
            $record->update([
                'status' => 'office_assistant_rejected',
                'rejected_by' => Auth::id(),
                'reject_reason' => $request->reject_reason,
            ]);
        }

        return redirect()->back()->with('success', 'Bulk rejected');
    }
}
