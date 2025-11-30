<?php
// app/Http/Controllers/ExElectrician/ReportController.php

namespace App\Http\Controllers\ExElectrician;

use App\Http\Controllers\Controller;
use App\Models\ExElectricianRenewApplication;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ApplicationsExport; // Custom export class

class ReportController extends Controller
{
    public function history(ExElectricianRenewApplication $record)
    {
        $this->authorize('viewHistory', $record);
        $histories = $record->histories()->orderBy('created_at', 'desc')->get();

        return view('ex-electrician.reports.history', ['histories' => $histories]);
    }

    public function exportPdf(Request $request)
    {
        $query = $this->buildFilteredQuery($request); // From filters
        $pdf = Pdf::loadView('ex-electrician.reports.pdf', ['applications' => $query->get()]);
        return $pdf->download('applications.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        return Excel::download(new ApplicationsExport($query->get()), 'applications.xlsx');
    }

    private function buildFilteredQuery(Request $request)
    {
        return ExElectricianRenewApplication::with(['entryBy', 'secretaryApprover'])
            ->when($request->status, fn($q) => $q->byStatus($request->status))
            // ... other filters: old_certificate_number, dates, district
            ->getQuery(); // Return query for chunking in export
    }
}
