<?php

namespace App\Exports;

use App\Models\ExElectricianRenewApplication;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ElectricianExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = ExElectricianRenewApplication::query()->with(['entryBy', 'approvedBySecretary']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['district'])) {
            $query->where('district', $this->filters['district']);
        }

        if (!empty($this->filters['division'])) {
            $query->where('division', $this->filters['division']);
        }

        if (!empty($this->filters['entry_by'])) {
            $query->where('entry_by', $this->filters['entry_by']);
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
            'Father Name',
            'Mother Name',
            'Mobile',
            'Email',
            'Date of Birth',
            'NID',
            'Village',
            'Post Office',
            'Postcode',
            'Upazilla',
            'District',
            'Division',
            'Degree',
            'Subject',
            'Board',
            'Academic Result',
            'Passing Year',
            'Company',
            'Designation',
            'Total Job Duration',
            'Certificate Number',
            'Issue Date',
            'Expiry Date',
            'Renewal Period',
            'Last Renewal Date',
            'Result',
            'Status',
            'Entry By',
            'Entry At',
            'Approved By',
            'Approved At',
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
            $application->father_name,
            $application->mother_name,
            $application->mobile_no,
            $application->email,
            $application->date_of_birth?->format('Y-m-d'),
            $application->nid_number,
            $application->village,
            $application->post_office,
            $application->postcode,
            $application->upazilla,
            $application->district,
            $application->division,
            $application->degree,
            $application->subject,
            $application->board,
            $application->academic_result,
            $application->passing_year,
            $application->company,
            $application->designation,
            $application->total_job_duration,
            $application->certificate_number,
            $application->issue_date?->format('Y-m-d'),
            $application->expiry_date?->format('Y-m-d'),
            $application->renewal_period,
            $application->last_renewal_date?->format('Y-m-d'),
            $application->result,
            $application->status_label,
            $application->entryBy?->name,
            $application->entry_at?->format('Y-m-d H:i:s'),
            $application->approvedBySecretary?->name,
            $application->approved_at_secretary?->format('Y-m-d H:i:s'),
            $application->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
