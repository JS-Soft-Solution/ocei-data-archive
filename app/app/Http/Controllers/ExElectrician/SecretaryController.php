<?php
// Similar to OfficeAssistant, but for secretary

namespace App\Http\Controllers\ExElectrician;

use App\Http\Controllers\Controller;
use App\Models\ExElectricianRenewApplication;
use Illuminate\Http\Request;

class SecretaryController extends Controller
{
    public function pending(Request $request)
    {
        $query = ExElectricianRenewApplication::with(['officeAssistantVerifier', 'entryBy'])
            ->pendingForSecretary();

        // Filters...
        return view('ex-electrician.secretary.pending', ['applications' => $query->paginate(20)]);
    }

    public function finalApprove(ExElectricianRenewApplication $record)
    {
        $this->authorize('approveAsSecretary', $record);
        $record->update([
            'status' => 'secretary_approved_final',
            'approved_by_secretary' => Auth::id(),
            'approved_at_secretary' => now(),
        ]); // Now locked via policy

        return redirect()->back()->with('success', 'Final approved & locked');
    }

    public function reject(Request $request, ExElectricianRenewApplication $record)
    {
        $this->authorize('rejectAsSecretary', $record);
        $request->validate(['reject_reason' => 'required|string|max:1000']);

        $record->update([
            'status' => 'secretary_rejected',
            'rejected_by' => Auth::id(),
            'reject_reason' => $request->reject_reason,
        ]); // Back to operator, direct resubmit to secretary

        return redirect()->back()->with('success', 'Rejected');
    }

    // bulkFinalApprove & bulkReject similar to above, with chunking for large sets
    public function bulkFinalApprove(Request $request)
    {
        $ids = $request->input('selected', []);
        ExElectricianRenewApplication::whereIn('id', $ids)
            ->chunkById(100, function ($records) {
                foreach ($records as $record) {
                    $this->authorize('approveAsSecretary', $record);
                    $record->update([
                        'status' => 'secretary_approved_final',
                        'approved_by_secretary' => Auth::id(),
                        'approved_at_secretary' => now(),
                    ]);
                }
            });

        return redirect()->back()->with('success', 'Bulk final approved');
    }
}
