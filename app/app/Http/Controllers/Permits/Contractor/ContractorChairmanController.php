<?php

namespace App\Http\Controllers\Permits\Contractor;

use App\Http\Controllers\Controller;
use App\Models\ExContractorRenewApplication;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ContractorChairmanController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display final approved applications (read-only).
     */
    public function index(Request $request)
    {
        $query = ExContractorRenewApplication::query()
            ->where('status', 'secretary_approved_final')
            ->with(['attachments', 'entryBy', 'approvedBySecretary']);

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

        $applications = $query->latest('approved_at_secretary')->paginate(20);

        return view('permits.contractor.chairman.index', compact('applications'));
    }

    /**
     * Display the specified application (read-only).
     */
    public function show(ExContractorRenewApplication $application)
    {
        $this->authorize('viewAsChairman', $application);

        $application->load([
            'attachments',
            'histories.performedBy',
            'entryBy',
            'verifiedByOfficeAssistant',
            'approvedBySecretary',
        ]);

        return view('permits.contractor.chairman.show', compact('application'));
    }
}
