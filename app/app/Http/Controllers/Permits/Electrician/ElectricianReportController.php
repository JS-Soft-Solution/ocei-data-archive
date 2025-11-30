<?php

namespace App\Http\Controllers\Permits\Electrician;

use App\Http\Controllers\Controller;
use App\Models\ExElectricianRenewApplication;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ElectricianExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ElectricianReportController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display report filter form.
     */
    public function index()
    {
        return view('permits.electrician.reports.index');
    }

    /**
     * Export to Excel.
     */
    public function exportExcel(Request $request)
    {
        $filters = $this->validateFilters($request);

        $fileName = 'electrician_applications_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new ElectricianExport($filters), $fileName);
    }

    /**
     * Export to PDF.
     */
    public function exportPdf(Request $request)
    {
        $filters = $this->validateFilters($request);

        $query = $this->buildQuery($filters);
        $applications = $query->get();

        $pdf = Pdf::loadView('permits.electrician.reports.pdf', compact('applications', 'filters'));

        $fileName = 'electrician_applications_' . now()->format('Y-m-d_His') . '.pdf';

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

        return view('permits.electrician.reports.preview', compact('applications', 'filters'));
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
        ]);
    }

    /**
     * Build query with filters.
     */
    protected function buildQuery(array $filters)
    {
        $query = ExElectricianRenewApplication::query()
            ->with(['entryBy', 'approvedBySecretary']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['district'])) {
            $query->where('district', $filters['district']);
        }

        if (!empty($filters['division'])) {
            $query->where('division', $filters['division']);
        }

        if (!empty($filters['entry_by'])) {
            $query->where('entry_by', $filters['entry_by']);
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->latest();
    }
}
