<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class BasePermitPolicy
{
    /**
     * Determine if user can view any permits.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole([
            'data_entry_operator',
            'office_assistant',
            'secretary',
            'chairman',
            'super_admin',
        ]);
    }

    /**
     * Determine if user can view the permit.
     */
    public function view(User $user, Model $permit): bool
    {
        // Super admin can view all
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Chairman can view only final approved
        if ($user->hasRole('chairman')) {
            return $permit->status === 'secretary_approved_final';
        }

        // Secretary can view submitted to secretary or final approved
        if ($user->hasRole('secretary')) {
            return in_array($permit->status, ['submitted_to_secretary', 'secretary_rejected', 'secretary_approved_final']);
        }

        // Office assistant can view submitted to them
        if ($user->hasRole('office_assistant')) {
            return in_array($permit->status, ['submitted_to_office_assistant', 'office_assistant_rejected', 'submitted_to_secretary', 'secretary_approved_final']);
        }

        // Data entry operator can view their own records
        if ($user->hasRole('data_entry_operator')) {
            return $permit->entry_by === $user->id;
        }

        return false;
    }

    /**
     * Determine if user can create permits.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('data_entry_operator');
    }

    /**
     * Determine if user can update the permit.
     */
    public function update(User $user, Model $permit): bool
    {
        // Super admin can override locked records
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Check if record is locked
        if ($permit->status === 'secretary_approved_final') {
            return false;
        }

        // Only owner can update draft or rejected records
        if ($user->hasRole('data_entry_operator')) {
            return $permit->entry_by === $user->id
                && in_array($permit->status, ['draft', 'office_assistant_rejected', 'secretary_rejected']);
        }

        return false;
    }

    /**
     * Determine if user can delete the permit.
     */
    public function delete(User $user, Model $permit): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if user can restore the permit.
     */
    public function restore(User $user, Model $permit): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if user can force delete the permit.
     */
    public function forceDelete(User $user, Model $permit): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if user can submit to office assistant.
     */
    public function submitToOfficeAssistant(User $user, Model $permit): bool
    {
        return $user->hasRole('data_entry_operator')
            && $permit->entry_by === $user->id
            && in_array($permit->status, ['draft', 'office_assistant_rejected', 'secretary_rejected'])
            && $permit->attachments()->count() > 0;
    }

    /**
     * Determine if user can resubmit after secretary rejection.
     */
    public function resubmitToSecretary(User $user, Model $permit): bool
    {
        return $user->hasRole('data_entry_operator')
            && $permit->entry_by === $user->id
            && $permit->status === 'secretary_rejected'
            && $permit->attachments()->count() > 0;
    }

    /**
     * Determine if office assistant can verify the permit.
     */
    public function verifyAsOfficeAssistant(User $user, Model $permit): bool
    {
        return $user->hasRole('office_assistant')
            && $permit->status === 'submitted_to_office_assistant';
    }

    /**
     * Determine if office assistant can reject the permit.
     */
    public function rejectAsOfficeAssistant(User $user, Model $permit): bool
    {
        return $user->hasRole('office_assistant')
            && $permit->status === 'submitted_to_office_assistant';
    }

    /**
     * Determine if secretary can approve the permit.
     */
    public function approveAsSecretary(User $user, Model $permit): bool
    {
        return $user->hasRole('secretary')
            && $permit->status === 'submitted_to_secretary';
    }

    /**
     * Determine if secretary can reject the permit.
     */
    public function rejectAsSecretary(User $user, Model $permit): bool
    {
        return $user->hasRole('secretary')
            && $permit->status === 'submitted_to_secretary';
    }

    /**
     * Determine if chairman can view the permit.
     */
    public function viewAsChairman(User $user, Model $permit): bool
    {
        return $user->hasRole('chairman')
            && $permit->status === 'secretary_approved_final';
    }

    /**
     * Determine if super admin can override.
     */
    public function superAdminOverride(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if user can upload attachments.
     */
    public function uploadAttachment(User $user, Model $permit): bool
    {
        // Super admin can upload anytime
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Owner can upload to draft or rejected records
        return $user->hasRole('data_entry_operator')
            && $permit->entry_by === $user->id
            && in_array($permit->status, ['draft', 'office_assistant_rejected', 'secretary_rejected']);
    }

    /**
     * Determine if user can delete attachments.
     */
    public function deleteAttachment(User $user, Model $permit): bool
    {
        // Super admin can delete anytime
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Owner can delete from draft or rejected records
        return $user->hasRole('data_entry_operator')
            && $permit->entry_by === $user->id
            && in_array($permit->status, ['draft', 'office_assistant_rejected', 'secretary_rejected']);
    }
}
