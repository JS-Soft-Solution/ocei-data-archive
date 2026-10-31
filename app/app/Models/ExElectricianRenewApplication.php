<?php

namespace App\Models;

use App\Traits\HasAuditHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class ExElectricianRenewApplication extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasAuditHistory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'old_certificate_number',
        'class',
        'book_number',
        'applicant_name_bn',
        'applicant_name_en',
        'father_name',
        'mother_name',
        'mobile_no',
        'email',
        'date_of_birth',
        'nid_number',
        'village',
        'post_office',
        'postcode',
        'upazilla',
        'district',
        'division',
        'degree',
        'subject',
        'board',
        'academic_result',
        'passing_year',
        'company',
        'designation',
        'total_job_duration',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'renewal_period',
        'last_renewal_date',
        'result',
        'status',
        'entry_by',
        'entry_at',
        'last_updated_by',
        'last_updated_at',
        'rejected_by',
        'reject_reason',
        'rejected_at',
        'verified_by_office_assistant',
        'verified_at_office_assistant',
        'approved_by_secretary',
        'approved_at_secretary',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'last_renewal_date' => 'date',
        'entry_at' => 'datetime',
        'last_updated_at' => 'datetime',
        'rejected_at' => 'datetime',
        'verified_at_office_assistant' => 'datetime',
        'approved_at_secretary' => 'datetime',
        'postcode' => 'integer',
        'renewal_period' => 'integer',
    ];

    /**
     * Get attachments.
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the user who created this entry.
     */
    public function entryBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entry_by');
    }

    /**
     * Get the user who last updated this entry.
     */
    public function lastUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    /**
     * Get the user who rejected this entry.
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the office assistant who verified this entry.
     */
    public function verifiedByOfficeAssistant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_office_assistant');
    }

    /**
     * Get the secretary who approved this entry.
     */
    public function approvedBySecretary(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_secretary');
    }

    /**
     * Get the user who deleted this entry.
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by entry user.
     */
    public function scopeByEntryUser(Builder $query, int $userId): Builder
    {
        return $query->where('entry_by', $userId);
    }

    /**
     * Scope to search by old certificate number or NID.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('old_certificate_number', 'like', "%{$search}%")
                ->orWhere('nid_number', 'like', "%{$search}%")
                ->orWhere('applicant_name_en', 'like', "%{$search}%")
                ->orWhere('applicant_name_bn', 'like', "%{$search}%")
                ->orWhere('mobile_no', 'like', "%{$search}%");
        });
    }

    /**
     * Check if the record is locked (secretary approved final).
     */
    public function getIsLockedAttribute(): bool
    {
        return $this->status === 'secretary_approved_final';
    }

    /**
     * Check if the record is locked (method form for views).
     */
    public function isLocked(): bool
    {
        return $this->status === 'secretary_approved_final';
    }

    /**
     * Check if the record can be edited.
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'office_assistant_rejected', 'secretary_rejected']);
    }

    /**
     * Check if the record can be submitted.
     */
    public function canBeSubmitted(): bool
    {
        return in_array($this->status, ['draft', 'office_assistant_rejected', 'secretary_rejected'])
            && $this->attachments()->count() > 0;
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'submitted_to_office_assistant' => 'info',
            'office_assistant_rejected' => 'warning',
            'submitted_to_secretary' => 'primary',
            'secretary_rejected' => 'danger',
            'secretary_approved_final' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get human-readable status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'submitted_to_office_assistant' => 'Submitted to Office Assistant',
            'office_assistant_rejected' => 'Rejected by Office Assistant',
            'submitted_to_secretary' => 'Submitted to Secretary',
            'secretary_rejected' => 'Rejected by Secretary',
            'secretary_approved_final' => 'Final Approved',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Log super admin override for audit trail.
     */
    public function logSuperAdminOverride(array $changedFields, string $reason = ''): void
    {
        // This method logs admin overrides to the audit history
        // Using the HasAuditHistory trait
        $changes = [];
        foreach ($changedFields as $field => $newValue) {
            $oldValue = $this->getOriginal($field);
            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            $this->recordAuditHistory(
                'super_admin_override',
                $reason,
                $changes
            );
        }
    }
}
