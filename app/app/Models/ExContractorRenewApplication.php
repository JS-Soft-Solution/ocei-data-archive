<?php
// app/Models/ExContractorRenewApplication.php

namespace App\Models;

use App\Models\Traits\HasAuditHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ExContractorRenewApplication extends Model
{
    use HasFactory, SoftDeletes, HasAuditHistory;

    protected $table = 'ex_contractor_renew_applications';

    protected $fillable = [
        // Contractor-specific
        'old_certificate_number', 'mobile_no', 'result', 'company_name_bn', 'company_name_en',
        'owner_shareholder_name', 'elb_certified_supervisor_no', 'company_type',
        'representative_name', 'representative_designation', 'email',
        // BOA fields: boa_village, boa_road, etc.
        'boa_village', 'boa_road', 'boa_post_office', 'boa_division', 'boa_district',
        'boa_upozila', 'boa_post_code',
        // BRA fields: bra_village, etc.
        'bra_village', 'bra_road', 'bra_post_office', 'bra_division', 'bra_district',
        'bra_upozila', 'bra_post_code',
        'company_registration_number', 'trade_license_number', 'village', 'postcode',
        'post_office', 'upazilla', 'district', 'division', 'degree',
        'et_serial_no', 'et_manufacturer_name', 'et_country_origin',
        'mg_serial_no', 'mg_manufacturer_name', 'mg_country_origin',
        'cm_serial_no', 'cm_manufacturer_name', 'cm_country_origin',
        'certificate_number', 'issue_date', 'expiry_date', 'last_renewal_date',
        // Workflow same
        'status', 'reject_reason', // etc.
        'supervisor_acknowledgement', 'renewal_period',
    ];

    protected $casts = [
        // Dates as above
        'issue_date' => 'date',
        // ...
    ];

    // Relationships & Scopes: Identical
}
