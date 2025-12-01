<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Notify when an application is submitted.
     */
    public static function notifyApplicationSubmitted($application, string $permitType): void
    {
        // Determine recipient based on new status
        $recipientRole = match ($application->status) {
            'submitted_to_office_assistant' => 'office_assistant',
            'submitted_to_secretary' => 'secretary',
            default => null,
        };

        if (!$recipientRole) {
            return;
        }

        // Get all users with this role
        $recipients = User::where('admin_type', $recipientRole)->get();

        foreach ($recipients as $recipient) {
            Notification::create([
                'user_id' => $recipient->id,
                'type' => 'application_submitted',
                'title' => 'New Application Submitted',
                'message' => sprintf(
                    'Application %s (%s) has been submitted for your review',
                    $application->old_certificate_number,
                    ucfirst($permitType)
                ),
                'data' => [
                    'application_id' => $application->id,
                    'permit_type' => $permitType,
                    'certificate_number' => $application->old_certificate_number,
                    'applicant_name' => $application->applicant_name_en ?? $application->applicant_name_bn,
                ],
            ]);
        }
    }

    /**
     * Notify when an application is approved.
     */
    public static function notifyApplicationApproved($application, string $permitType, string $approvedBy): void
    {
        if (!$application->entry_by) {
            return; // No one to notify
        }

        Notification::create([
            'user_id' => $application->entry_by,
            'type' => 'application_approved',
            'title' => 'Application Approved',
            'message' => sprintf(
                'Your application %s (%s) has been approved by %s',
                $application->old_certificate_number,
                ucfirst($permitType),
                $approvedBy
            ),
            'data' => [
                'application_id' => $application->id,
                'permit_type' => $permitType,
                'certificate_number' => $application->old_certificate_number,
                'applicant_name' => $application->applicant_name_en ?? $application->applicant_name_bn,
            ],
        ]);
    }

    /**
     * Notify when an application is rejected.
     */
    public static function notifyApplicationRejected($application, string $permitType, string $rejectedBy, ?string $reason = null): void
    {
        if (!$application->entry_by) {
            return; // No one to notify
        }

        $message = sprintf(
            'Your application %s (%s) has been rejected by %s',
            $application->old_certificate_number,
            ucfirst($permitType),
            $rejectedBy
        );

        if ($reason) {
            $message .= '. Reason: ' . $reason;
        }

        Notification::create([
            'user_id' => $application->entry_by,
            'type' => 'application_rejected',
            'title' => 'Application Rejected',
            'message' => $message,
            'data' => [
                'application_id' => $application->id,
                'permit_type' => $permitType,
                'certificate_number' => $application->old_certificate_number,
                'applicant_name' => $application->applicant_name_en ?? $application->applicant_name_bn,
                'rejection_reason' => $reason,
            ],
        ]);
    }

    /**
     * Notify when an application is forwarded to secretary.
     */
    public static function notifyApplicationForwardedToSecretary($application, string $permitType): void
    {
        // Get all secretaries
        $secretaries = User::where('admin_type', 'secretary')->get();

        foreach ($secretaries as $secretary) {
            Notification::create([
                'user_id' => $secretary->id,
                'type' => 'application_submitted',
                'title' => 'Application Forwarded for Final Approval',
                'message' => sprintf(
                    'Application %s (%s) has been verified and forwarded for final approval',
                    $application->old_certificate_number,
                    ucfirst($permitType)
                ),
                'data' => [
                    'application_id' => $application->id,
                    'permit_type' => $permitType,
                    'certificate_number' => $application->old_certificate_number,
                    'applicant_name' => $application->applicant_name_en ?? $application->applicant_name_bn,
                ],
            ]);
        }
    }
}
