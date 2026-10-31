<?php

namespace App\Traits;

use App\Models\RecordHistory;
use Illuminate\Support\Facades\Auth;

trait HasAuditHistory
{
    /**
     * Boot the trait.
     */
    protected static function bootHasAuditHistory(): void
    {
        static::created(function ($model) {
            $model->logHistory('created', [], $model->getAttributes());
        });

        static::updating(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getDirty();

            // Log status changes separately
            if (isset($changes['status']) && $original['status'] !== $changes['status']) {
                $model->logHistory('status_changed', [
                    'old_status' => $original['status'],
                ], [
                    'new_status' => $changes['status'],
                ]);
            }

            // Log general updates
            if (!empty($changes)) {
                $oldValues = array_intersect_key($original, $changes);
                $model->logHistory('updated', $oldValues, $changes);
            }
        });

        static::deleting(function ($model) {
            if ($model->isForceDeleting()) {
                $model->logHistory('force_deleted', $model->getAttributes(), []);
            } else {
                $model->logHistory('soft_deleted', $model->getAttributes(), [
                    'deleted_by' => Auth::id(),
                ]);
            }
        });

        static::restored(function ($model) {
            $model->logHistory('restored', [
                'deleted_at' => $model->getOriginal('deleted_at'),
            ], [
                'deleted_at' => null,
            ]);
        });
    }

    /**
     * Log a history record.
     */
    public function logHistory(string $action, array $oldValues = [], array $newValues = [], ?string $notes = null): void
    {
        $this->histories()->create([
            'action' => $action,
            'old_values' => !empty($oldValues) ? $oldValues : null,
            'new_values' => !empty($newValues) ? $newValues : null,
            'notes' => $notes,
            'performed_by' => Auth::id(),
            'performed_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log attachment addition.
     */
    public function logAttachmentAdded(string $fileName): void
    {
        $this->logHistory('attachment_added', [], ['file_name' => $fileName]);
    }

    /**
     * Log attachment deletion.
     */
    public function logAttachmentDeleted(string $fileName): void
    {
        $this->logHistory('attachment_deleted', ['file_name' => $fileName], []);
    }

    /**
     * Log super admin override.
     */
    public function logSuperAdminOverride(array $changes, string $reason): void
    {
        $this->logHistory('super_admin_override', $this->getOriginal(), $changes, $reason);
    }

    /**
     * Get all history records.
     */
    public function histories()
    {
        return $this->morphMany(RecordHistory::class, 'historyable');
    }
}
