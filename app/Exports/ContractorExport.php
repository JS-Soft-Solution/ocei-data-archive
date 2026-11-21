<?php

namespace App\Exports;

use App\Models\ExContractorRenewApplication;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ContractorExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = ExContractorRenewApplication::query()->with(['entryBy', 'approvedBySecretary']);

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
            'Company Name (Bangla)',
            'Company Name (English)',
            'Owner/Shareholder',
            'Company Type',
            'Mobile',
            'Email',
            'Representative',
            'Business Office District',
            'Supervisor Certificate #',
            'Equipment Testing Serial',
            'Megger Serial',
            'Clamp Meter Serial',
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
            $application->company_name_bn,
            $application->company_name_en,
            $application->owner_shareholder_name,
            $application->company_type,
            $application->mobile_no,
            $application->email,
            $application->representative_name,
            $application->boa_district,
            $application->elb_certified_supervisor_no,
            $application->et_serial_no,
            $application->mg_serial_no,
            $application->cm_serial_no,
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
