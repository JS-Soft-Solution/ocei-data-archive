<?php
// app/Models/Traits/HasAuditHistory.php

namespace App\Models\Traits;

use App\Models\RecordHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasAuditHistory
{
    protected array $auditAttributes = []; // Override in model to specify tracked fields, e.g., ['status', 'old_certificate_number']

    public static function bootHasAuditHistory(): void
    {
        static::creating(function (Model $model) {
            self::logChange($model, 'created', []);
        });

        static::updating(function (Model $model) {
            $dirty = $model->getDirty();
            $original = $model->getOriginal();
            $changes = [];
            foreach ($dirty as $key => $value) {
                if (in_array($key, $model->getAuditAttributes())) {
                    $changes[$key] = ['old' => $original[$key] ?? null, 'new' => $value];
                }
            }
            $action = count($changes) > 0 ? 'updated' : 'no_change';
            if (isset($changes['status'])) {
                $action = 'status_changed';
            }
            self::logChange($model, $action, $changes);
        });

        static::saved(function (Model $model) {
            // For attachments or other post-save (e.g., if added separately)
            if (request()->has('attachments')) {
                self::logChange($model, 'attachment_added', []);
            }
        });

        static::deleting(function (Model $model) {
            if ($model->trashed()) { // Soft delete
                self::logChange($model, 'soft_deleted', []);
            }
        });
    }

    protected function getAuditAttributes(): array
    {
        return $this->auditAttributes ?: $this->fillable;
    }

    protected static function logChange(Model $model, string $action, array $changes): void
    {
        if (!Auth::check()) return;

        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super_admin'); // Assumes Spatie; adjust to your role check

        if ($isSuperAdmin && $model->status === 'secretary_approved_final') {
            $action = 'super_admin_override';
        }

        RecordHistory::create([
            'historable_id' => $model->id,
            'historable_type' => get_class($model),
            'changed_by' => $user->id,
            'changed_role' => $user->roles->first()?->name ?? 'unknown', // Adjust to your role retrieval
            'action' => $action,
            'old_values' => collect($changes)->map(fn($change) => $change['old'])->toArray(),
            'new_values' => collect($changes)->map(fn($change) => $change['new'])->toArray(),
        ]);
    }
}
