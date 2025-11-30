<?php
// app/Models/ExElectricianRenewApplication.php

namespace App\Models;

use App\Models\Traits\HasAuditHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ExElectricianRenewApplication extends Model
{
    use HasFactory, SoftDeletes, HasAuditHistory;

    protected $table = 'ex_electrician_renew_applications';

    protected $fillable = [
        'applicant_name_bn', 'old_certificate_number', 'mobile_no', 'result', 'applicant_name_en',
        'father_name', 'mother_name', 'email', 'date_of_birth', 'nid_number', 'village',
        'postcode', 'post_office', 'upazilla', 'district', 'division', 'degree', 'subject',
        'board', 'academic_result', 'passing_year', 'company', 'designation', 'total_job_duration',
        'certificate_number', 'issue_date', 'renewal_period', 'expiry_date', 'last_renewal_date',
        'status', 'reject_reason', 'entry_by', 'entry_at', 'last_updated_by', 'last_updated_at',
        'verified_by_office_assistant', 'verified_at_office_assistant', 'approved_by_secretary',
        'approved_at_secretary', 'application_status', 'renewal_status', 'inspector_approval',
        'secretary_approval', 'chairman_approval', 'application_created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'last_renewal_date' => 'date',
        'entry_at' => 'datetime',
        'last_updated_at' => 'datetime',
        'verified_at_office_assistant' => 'datetime',
        'approved_at_secretary' => 'datetime',
    ];

    // Relationships
    public function entryBy(): BelongsTo { return $this->belongsTo(User::class, 'entry_by'); }
    public function lastUpdatedBy(): BelongsTo { return $this->belongsTo(User::class, 'last_updated_by'); }
    public function rejectedByUser(): BelongsTo { return $this->belongsTo(User::class, 'rejected_by'); }
    public function officeAssistantVerifier(): BelongsTo { return $this->belongsTo(User::class, 'verified_by_office_assistant'); }
    public function secretaryApprover(): BelongsTo { return $this->belongsTo(User::class, 'approved_by_secretary'); }
    public function attachments(): MorphMany { return $this->morphMany(Attachment::class, 'attachable'); }
    public function histories(): MorphMany { return $this->morphMany(RecordHistory::class, 'historable'); }

    // Query Scopes
    public function scopeForOperator($query, User $user): void { $query->where('entry_by', $user->id); }
    public function scopePendingForOfficeAssistant($query): void { $query->where('status', 'submitted_to_office_assistant'); }
    public function scopePendingForSecretary($query): void { $query->where('status', 'submitted_to_secretary'); }
    public function scopeApproved($query): void { $query->where('status', 'secretary_approved_final'); }
    public function scopeRejected($query): void { $query->whereIn('status', ['office_assistant_rejected', 'secretary_rejected']); }
    public function scopeByStatus($query, string $status): void { $query->where('status', $status); }
    public function scopeByOldCertificate($query, string $number): void { $query->where('old_certificate_number', $number); }
}
