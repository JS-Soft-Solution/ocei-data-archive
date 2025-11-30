<?php
// app/Models/ExSupervisorRenewApplication.php

namespace App\Models;

use App\Models\Traits\HasAuditHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ExSupervisorRenewApplication extends Model
{
    use HasFactory, SoftDeletes, HasAuditHistory;

    protected $table = 'ex_supervisor_renew_applications';

    protected $fillable = [
        // All common + 'engagement_status_with_contractor', 'contractor_id'
        'applicant_name_bn', 'old_certificate_number', 'mobile_no', 'result', 'applicant_name_en',
        // ... (same as electrician, plus extras)
        'engagement_status_with_contractor', 'contractor_id',
    ];

    protected $casts = [
        // Same as electrician, but 'date_of_birth' => 'string'
        'date_of_birth' => 'string',
        // ... dates
    ];

    // Relationships & Scopes: Identical to ExElectricianRenewApplication
    public function entryBy(): BelongsTo { return $this->belongsTo(User::class, 'entry_by'); }
    // ... (copy from above)
}
