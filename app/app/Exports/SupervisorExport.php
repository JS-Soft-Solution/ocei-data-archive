<?php

namespace App\Exports;

use App\Models\ExSupervisorRenewApplication;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SupervisorExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = ExSupervisorRenewApplication::query()->with(['entryBy', 'approvedBySecretary']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['search'])) {
            $query->search($this->filters['search']);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Old Certificate Number',
            'Applicant Name (Bangla)',
            'Applicant Name (English)',
            'Mobile',
            'NID',
            'District',
            'Division',
            'Company',
            'Designation',
            'Contractor Engagement',
            'Certificate Number',
            'Issue Date',
            'Expiry Date',
            'Status',
            'Entry By',
            'Approved By',
            'Created At',
        ];
    }

    public function map($application): array
    {
        return [
            $application->id,
            $application->old_certificate_number,
            $application->applicant_name_bn,
            $application->applicant_name_en,
            $application->mobile_no,
            $application->nid_number,
            $application->district,
            $application->division,
            $application->company,
            $application->designation,
            $application->engagement_status_with_contractor,
            $application->certificate_number,
            $application->issue_date?->format('Y-m-d'),
            $application->expiry_date?->format('Y-m-d'),
            $application->status_label,
            $application->entryBy?->name,
            $application->approvedBySecretary?->name,
            $application->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
