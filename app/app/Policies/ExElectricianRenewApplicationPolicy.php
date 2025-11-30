<?php
// app/Policies/ExElectricianRenewApplicationPolicy.php

namespace App\Policies;

use App\Models\ExElectricianRenewApplication;
use App\Models\User;

class ExElectricianRenewApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['data_entry_operator', 'office_assistant', 'secretary', 'chairman', 'super_admin']);
    }

    public function view(User $user, ExElectricianRenewApplication $record): bool
    {
        if ($user->hasRole('super_admin')) return true;
        if ($user->hasRole('chairman') && !$record->approved()) return false; // Chairman only final

        return $record->entry_by === $user->id || // Operator owns
            $user->hasRole('office_assistant') || // OA sees pending/rejected
            $user->hasRole('secretary'); // Secretary sees all in workflow
    }

    public function create(User $user): bool
    {
        return $user->hasRole('data_entry_operator') || $user->hasRole('super_admin');
    }

    public function update(User $user, ExElectricianRenewApplication $record): bool
    {
        if ($user->hasRole('super_admin')) return true; // Override lock
        if ($record->isLocked()) return false; // Locked after final approval

        return $record->entry_by === $user->id && // Only owner for drafts/rejected
            in_array($record->status, ['draft', 'office_assistant_rejected', 'secretary_rejected']);
    }

    public function submit(User $user, ExElectricianRenewApplication $record): bool
    {
        return $user->hasRole('data_entry_operator') &&
            $record->entry_by === $user->id &&
            in_array($record->status, ['draft', 'office_assistant_rejected', 'secretary_rejected']);
    }

    public function approveAsOfficeAssistant(User $user, ExElectricianRenewApplication $record): bool
    {
        return $user->hasRole('office_assistant') && $record->status === 'submitted_to_office_assistant';
    }

    public function rejectAsOfficeAssistant(User $user, ExElectricianRenewApplication $record): bool
    {
        return $this->approveAsOfficeAssistant($user, $record); // Same condition
    }

    public function approveAsSecretary(User $user, ExElectricianRenewApplication $record): bool
    {
        return $user->hasRole('secretary') && $record->status === 'submitted_to_secretary';
    }

    public function rejectAsSecretary(User $user, ExElectricianRenewApplication $record): bool
    {
        return $this->approveAsSecretary($user, $record);
    }

    public function viewHistory(User $user, ExElectricianRenewApplication $record): bool
    {
        return $user->hasAnyRole(['secretary', 'chairman', 'super_admin']) || // View for these
            ($user->hasRole('data_entry_operator') && $record->entry_by === $user->id); // Owner views own
    }

    // Helper (not a policy method, but callable)
    public function isLocked(ExElectricianRenewApplication $record): bool
    {
        return $record->status === 'secretary_approved_final';
    }
}
