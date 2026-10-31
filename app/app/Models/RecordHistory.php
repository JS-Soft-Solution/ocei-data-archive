<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class RecordHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'historyable_type',
        'historyable_id',
        'action',
        'old_values',
        'new_values',
        'notes',
        'performed_by',
        'performed_at',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'performed_at' => 'datetime',
    ];

    /**
     * Get the parent historyable model.
     */
    public function historyable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who performed the action.
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Scope to filter by action type.
     */
    public function scopeByAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter status changes.
     */
    public function scopeStatusChanges(Builder $query): Builder
    {
        return $query->where('action', 'status_changed');
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeInDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('performed_at', [$startDate, $endDate]);
    }

    /**
     * Get a formatted description of the action.
     */
    public function getActionDescriptionAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Record created',
            'updated' => 'Record updated',
            'status_changed' => 'Status changed from "' . $this->getReadableStatus($this->old_values['old_status'] ?? 'unknown') . '" to "' . $this->getReadableStatus($this->new_values['new_status'] ?? 'unknown') . '"',
            'attachment_added' => 'Attachment added: ' . ($this->new_values['file_name'] ?? 'file'),
            'attachment_deleted' => 'Attachment deleted: ' . ($this->old_values['file_name'] ?? 'file'),
            'super_admin_override' => 'Super admin override',
            'soft_deleted' => 'Record soft deleted',
            'restored' => 'Record restored',
            'force_deleted' => 'Record permanently deleted',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    /**
     * Get human-readable status label.
     */
    protected function getReadableStatus(string $status): string
    {
        return match ($status) {
            'draft' => 'Draft',
            'submitted_to_office_assistant' => 'Submitted to Office Assistant',
            'office_assistant_rejected' => 'Rejected by Office Assistant',
            'submitted_to_secretary' => 'Submitted to Secretary',
            'secretary_rejected' => 'Rejected by Secretary',
            'secretary_approved_final' => 'Final Approved',
            'unknown' => 'Unknown',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * Get changed fields in a human-readable format.
     */
    public function getChangedFieldsAttribute(): array
    {
        if (empty($this->new_values)) {
            return [];
        }

        $changed = [];
        foreach ($this->new_values as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;
            if ($oldValue !== $newValue && !in_array($key, ['updated_at', 'last_updated_at', 'last_updated_by'])) {
                $changed[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changed;
    }
}
