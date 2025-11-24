<?php

namespace App\Traits;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait HasWorkflow
{
    public function entryUser() { return $this->belongsTo(User::class, 'entry_by'); }
    public function verifiedUser() { return $this->belongsTo(User::class, 'verified_by'); }
    public function approvedUser() { return $this->belongsTo(User::class, 'approved_by'); }
    public function rejectedUser() { return $this->belongsTo(User::class, 'rejected_by'); }

    public function audits()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }

    // Helper to log audit
    public function logAudit($action, $comments = null)
    {
        $this->audits()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'comments' => $comments
        ]);
    }
}
