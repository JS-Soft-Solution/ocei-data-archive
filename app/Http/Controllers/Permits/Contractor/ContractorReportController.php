<?php

namespace App\Http\Controllers\Permits\Contractor;

use App\Http\Controllers\Controller;
use App\Models\ExContractorRenewApplication;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContractorExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ContractorReportController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display report filter form.
     */
    public function index()
    {
        return view('permits.contractor.reports.index');
    }

    /**
     * Export to Excel.
     */
    public function exportExcel(Request $request)
    {
        $filters = $this->validateFilters($request);

        $fileName = 'contractor_applications_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new ContractorExport($filters), $fileName);
    }

    /**
     * Export to PDF.
     */
    public function exportPdf(Request $request)
    {
        $filters = $this->validateFilters($request);

        $query = $this->buildQuery($filters);
        $applications = $query->get();

        $pdf = Pdf::loadView('permits.contractor.reports.pdf', compact('applications', 'filters'));

        $fileName = 'contractor_applications_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Preview report.
     */
    public function preview(Request $request)
    {
        $filters = $this->validateFilters($request);

        $query = $this->buildQuery($filters);
        $applications = $query->paginate(50);

        return view('permits.contractor.reports.preview', compact('applications', 'filters'));
    }

    /**
     * Validate filter inputs.
     */
    protected function validateFilters(Request $request): array
    {
        return $request->validate([
            'status' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'district' => 'nullable|string',
            'division' => 'nullable|string',
            'entry_by' => 'nullable|exists:users,id',
            'search' => 'nullable|string',
            'book_number' => 'nullable|string',
            'class' => 'nullable|string',
            'year' => 'nullable|integer',
        ]);
    }

    /**
     * Build query with filters.
     */
    protected function buildQuery(array $filters)
    {
        $query = ExContractorRenewApplication::query()
            ->with(['entryBy', 'approvedBySecretary'])
            ->filter($filters);

        if (!empty($filters['entry_by'])) {
            $query->where('entry_by', $filters['entry_by']);
        }

        return $query->latest();
    }
}
