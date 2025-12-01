<?php

namespace App\Http\Controllers;

use App\Models\ExElectricianRenewApplication;
use App\Models\ExSupervisorRenewApplication;
use App\Models\ExContractorRenewApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard based on user role.
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->admin_type;

        // Aggregate statistics based on role
        $stats = $this->getStatsForRole($role, $user);

        // Return role-specific view
        return match ($role) {
            'data_entry_operator' => view('dashboards.operator_dashboard', $stats),
            'office_assistant' => view('dashboards.office_assistant_dashboard', $stats),
            'secretary' => view('dashboards.secretary_dashboard', $stats),
            'chairman' => view('dashboards.chairman_dashboard', $stats),
            'super_admin', 'system_admin' => view('dashboards.super_admin_dashboard', $stats),
            default => view('dashboard', $stats), // Fallback to default dashboard
        };
    }

    /**
     * Get statistics based on user role.
     */
    private function getStatsForRole(string $role, $user): array
    {
        return match ($role) {
            'data_entry_operator' => $this->getOperatorStats($user),
            'office_assistant' => $this->getOfficeAssistantStats(),
            'secretary' => $this->getSecretaryStats(),
            'chairman' => $this->getChairmanStats(),
            'super_admin', 'system_admin' => $this->getSuperAdminStats(),
            default => [],
        };
    }

    /**
     * Get statistics for data entry operator.
     */
    private function getOperatorStats($user): array
    {
        $userId = $user->id;

        return [
            'electrician' => [
                'draft' => ExElectricianRenewApplication::where('entry_by', $userId)
                    ->where('status', 'draft')->count(),
                'pending' => ExElectricianRenewApplication::where('entry_by', $userId)
                    ->where('status', 'submitted_to_office_assistant')->count(),
                'rejected' => ExElectricianRenewApplication::where('entry_by', $userId)
                    ->whereIn('status', ['office_assistant_rejected', 'secretary_rejected'])->count(),
                'approved' => ExElectricianRenewApplication::where('entry_by', $userId)
                    ->where('status', 'secretary_approved_final')->count(),
            ],
            'supervisor' => [
                'draft' => ExSupervisorRenewApplication::where('entry_by', $userId)
                    ->where('status', 'draft')->count(),
                'pending' => ExSupervisorRenewApplication::where('entry_by', $userId)
                    ->where('status', 'submitted_to_office_assistant')->count(),
                'rejected' => ExSupervisorRenewApplication::where('entry_by', $userId)
                    ->whereIn('status', ['office_assistant_rejected', 'secretary_rejected'])->count(),
                'approved' => ExSupervisorRenewApplication::where('entry_by', $userId)
                    ->where('status', 'secretary_approved_final')->count(),
            ],
            'contractor' => [
                'draft' => ExContractorRenewApplication::where('entry_by', $userId)
                    ->where('status', 'draft')->count(),
                'pending' => ExContractorRenewApplication::where('entry_by', $userId)
                    ->where('status', 'submitted_to_office_assistant')->count(),
                'rejected' => ExContractorRenewApplication::where('entry_by', $userId)
                    ->whereIn('status', ['office_assistant_rejected', 'secretary_rejected'])->count(),
                'approved' => ExContractorRenewApplication::where('entry_by', $userId)
                    ->where('status', 'secretary_approved_final')->count(),
            ],
            'total' => [
                'draft' => ExElectricianRenewApplication::where('entry_by', $userId)->where('status', 'draft')->count() +
                    ExSupervisorRenewApplication::where('entry_by', $userId)->where('status', 'draft')->count() +
                    ExContractorRenewApplication::where('entry_by', $userId)->where('status', 'draft')->count(),
                'pending' => ExElectricianRenewApplication::where('entry_by', $userId)->where('status', 'submitted_to_office_assistant')->count() +
                    ExSupervisorRenewApplication::where('entry_by', $userId)->where('status', 'submitted_to_office_assistant')->count() +
                    ExContractorRenewApplication::where('entry_by', $userId)->where('status', 'submitted_to_office_assistant')->count(),
                'rejected' => ExElectricianRenewApplication::where('entry_by', $userId)->whereIn('status', ['office_assistant_rejected', 'secretary_rejected'])->count() +
                    ExSupervisorRenewApplication::where('entry_by', $userId)->whereIn('status', ['office_assistant_rejected', 'secretary_rejected'])->count() +
                    ExContractorRenewApplication::where('entry_by', $userId)->whereIn('status', ['office_assistant_rejected', 'secretary_rejected'])->count(),
                'approved' => ExElectricianRenewApplication::where('entry_by', $userId)->where('status', 'secretary_approved_final')->count() +
                    ExSupervisorRenewApplication::where('entry_by', $userId)->where('status', 'secretary_approved_final')->count() +
                    ExContractorRenewApplication::where('entry_by', $userId)->where('status', 'secretary_approved_final')->count(),
            ],
            'recent_applications' => $this->getRecentApplicationsForOperator($userId),
        ];
    }

    /**
     * Get statistics for office assistant.
     */
    private function getOfficeAssistantStats(): array
    {
        return [
            'electrician' => [
                'pending' => ExElectricianRenewApplication::where('status', 'submitted_to_office_assistant')->count(),
                'approved_today' => ExElectricianRenewApplication::where('status', 'submitted_to_secretary')
                    ->whereDate('verified_at_office_assistant', today())->count(),
                'rejected_today' => ExElectricianRenewApplication::where('status', 'office_assistant_rejected')
                    ->whereDate('rejected_at', today())->count(),
            ],
            'supervisor' => [
                'pending' => ExSupervisorRenewApplication::where('status', 'submitted_to_office_assistant')->count(),
                'approved_today' => ExSupervisorRenewApplication::where('status', 'submitted_to_secretary')
                    ->whereDate('verified_at_office_assistant', today())->count(),
                'rejected_today' => ExSupervisorRenewApplication::where('status', 'office_assistant_rejected')
                    ->whereDate('rejected_at', today())->count(),
            ],
            'contractor' => [
                'pending' => ExContractorRenewApplication::where('status', 'submitted_to_office_assistant')->count(),
                'approved_today' => ExContractorRenewApplication::where('status', 'submitted_to_secretary')
                    ->whereDate('verified_at_office_assistant', today())->count(),
                'rejected_today' => ExContractorRenewApplication::where('status', 'office_assistant_rejected')
                    ->whereDate('rejected_at', today())->count(),
            ],
            'total' => [
                'pending' => ExElectricianRenewApplication::where('status', 'submitted_to_office_assistant')->count() +
                    ExSupervisorRenewApplication::where('status', 'submitted_to_office_assistant')->count() +
                    ExContractorRenewApplication::where('status', 'submitted_to_office_assistant')->count(),
                'processed_this_month' => $this->getMonthlyProcessedByOfficeAssistant(),
            ],
            'pending_applications' => $this->getPendingApplicationsForOfficeAssistant(),
        ];
    }

    /**
     * Get statistics for secretary.
     */
    private function getSecretaryStats(): array
    {
        return [
            'electrician' => [
                'pending' => ExElectricianRenewApplication::where('status', 'submitted_to_secretary')->count(),
                'approved_this_month' => ExElectricianRenewApplication::where('status', 'secretary_approved_final')
                    ->whereMonth('approved_at_secretary', now()->month)
                    ->whereYear('approved_at_secretary', now()->year)->count(),
                'rejected_this_month' => ExElectricianRenewApplication::where('status', 'secretary_rejected')
                    ->whereMonth('rejected_at', now()->month)
                    ->whereYear('rejected_at', now()->year)->count(),
            ],
            'supervisor' => [
                'pending' => ExSupervisorRenewApplication::where('status', 'submitted_to_secretary')->count(),
                'approved_this_month' => ExSupervisorRenewApplication::where('status', 'secretary_approved_final')
                    ->whereMonth('approved_at_secretary', now()->month)
                    ->whereYear('approved_at_secretary', now()->year)->count(),
                'rejected_this_month' => ExSupervisorRenewApplication::where('status', 'secretary_rejected')
                    ->whereMonth('rejected_at', now()->month)
                    ->whereYear('rejected_at', now()->year)->count(),
            ],
            'contractor' => [
                'pending' => ExContractorRenewApplication::where('status', 'submitted_to_secretary')->count(),
                'approved_this_month' => ExContractorRenewApplication::where('status', 'secretary_approved_final')
                    ->whereMonth('approved_at_secretary', now()->month)
                    ->whereYear('approved_at_secretary', now()->year)->count(),
                'rejected_this_month' => ExContractorRenewApplication::where('status', 'secretary_rejected')
                    ->whereMonth('rejected_at', now()->month)
                    ->whereYear('rejected_at', now()->year)->count(),
            ],
            'total' => [
                'pending' => ExElectricianRenewApplication::where('status', 'submitted_to_secretary')->count() +
                    ExSupervisorRenewApplication::where('status', 'submitted_to_secretary')->count() +
                    ExContractorRenewApplication::where('status', 'submitted_to_secretary')->count(),
                'approved_this_month' => ExElectricianRenewApplication::where('status', 'secretary_approved_final')
                    ->whereMonth('approved_at_secretary', now()->month)->whereYear('approved_at_secretary', now()->year)->count() +
                    ExSupervisorRenewApplication::where('status', 'secretary_approved_final')
                        ->whereMonth('approved_at_secretary', now()->month)->whereYear('approved_at_secretary', now()->year)->count() +
                    ExContractorRenewApplication::where('status', 'secretary_approved_final')
                        ->whereMonth('approved_at_secretary', now()->month)->whereYear('approved_at_secretary', now()->year)->count(),
            ],
            'pending_applications' => $this->getPendingApplicationsForSecretary(),
        ];
    }

    /**
     * Get statistics for chairman.
     */
    private function getChairmanStats(): array
    {
        return [
            'electrician' => [
                'total_approved' => ExElectricianRenewApplication::where('status', 'secretary_approved_final')->count(),
                'approved_this_month' => ExElectricianRenewApplication::where('status', 'secretary_approved_final')
                    ->whereMonth('approved_at_secretary', now()->month)
                    ->whereYear('approved_at_secretary', now()->year)->count(),
            ],
            'supervisor' => [
                'total_approved' => ExSupervisorRenewApplication::where('status', 'secretary_approved_final')->count(),
                'approved_this_month' => ExSupervisorRenewApplication::where('status', 'secretary_approved_final')
                    ->whereMonth('approved_at_secretary', now()->month)
                    ->whereYear('approved_at_secretary', now()->year)->count(),
            ],
            'contractor' => [
                'total_approved' => ExContractorRenewApplication::where('status', 'secretary_approved_final')->count(),
                'approved_this_month' => ExContractorRenewApplication::where('status', 'secretary_approved_final')
                    ->whereMonth('approved_at_secretary', now()->month)
                    ->whereYear('approved_at_secretary', now()->year)->count(),
            ],
            'total' => [
                'approved' => ExElectricianRenewApplication::where('status', 'secretary_approved_final')->count() +
                    ExSupervisorRenewApplication::where('status', 'secretary_approved_final')->count() +
                    ExContractorRenewApplication::where('status', 'secretary_approved_final')->count(),
                'approved_this_month' => ExElectricianRenewApplication::where('status', 'secretary_approved_final')
                    ->whereMonth('approved_at_secretary', now()->month)->whereYear('approved_at_secretary', now()->year)->count() +
                    ExSupervisorRenewApplication::where('status', 'secretary_approved_final')
                        ->whereMonth('approved_at_secretary', now()->month)->whereYear('approved_at_secretary', now()->year)->count() +
                    ExContractorRenewApplication::where('status', 'secretary_approved_final')
                        ->whereMonth('approved_at_secretary', now()->month)->whereYear('approved_at_secretary', now()->year)->count(),
            ],
            'recent_approvals' => $this->getRecentApprovals(),
        ];
    }

    /**
     * Get statistics for super admin.
     */
    private function getSuperAdminStats(): array
    {
        return [
            'electrician' => [
                'total' => ExElectricianRenewApplication::count(),
                'draft' => ExElectricianRenewApplication::where('status', 'draft')->count(),
                'pending_oa' => ExElectricianRenewApplication::where('status', 'submitted_to_office_assistant')->count(),
                'pending_secretary' => ExElectricianRenewApplication::where('status', 'submitted_to_secretary')->count(),
                'approved' => ExElectricianRenewApplication::where('status', 'secretary_approved_final')->count(),
                'rejected' => ExElectricianRenewApplication::whereIn('status', ['office_assistant_rejected', 'secretary_rejected'])->count(),
            ],
            'supervisor' => [
                'total' => ExSupervisorRenewApplication::count(),
                'draft' => ExSupervisorRenewApplication::where('status', 'draft')->count(),
                'pending_oa' => ExSupervisorRenewApplication::where('status', 'submitted_to_office_assistant')->count(),
                'pending_secretary' => ExSupervisorRenewApplication::where('status', 'submitted_to_secretary')->count(),
                'approved' => ExSupervisorRenewApplication::where('status', 'secretary_approved_final')->count(),
                'rejected' => ExSupervisorRenewApplication::whereIn('status', ['office_assistant_rejected', 'secretary_rejected'])->count(),
            ],
            'contractor' => [
                'total' => ExContractorRenewApplication::count(),
                'draft' => ExContractorRenewApplication::where('status', 'draft')->count(),
                'pending_oa' => ExContractorRenewApplication::where('status', 'submitted_to_office_assistant')->count(),
                'pending_secretary' => ExContractorRenewApplication::where('status', 'submitted_to_secretary')->count(),
                'approved' => ExContractorRenewApplication::where('status', 'secretary_approved_final')->count(),
                'rejected' => ExContractorRenewApplication::whereIn('status', ['office_assistant_rejected', 'secretary_rejected'])->count(),
            ],
            'total' => [
                'applications' => ExElectricianRenewApplication::count() +
                    ExSupervisorRenewApplication::count() +
                    ExContractorRenewApplication::count(),
                'users' => User::count(),
                'approved_this_month' => ExElectricianRenewApplication::where('status', 'secretary_approved_final')
                    ->whereMonth('approved_at_secretary', now()->month)->whereYear('approved_at_secretary', now()->year)->count() +
                    ExSupervisorRenewApplication::where('status', 'secretary_approved_final')
                        ->whereMonth('approved_at_secretary', now()->month)->whereYear('approved_at_secretary', now()->year)->count() +
                    ExContractorRenewApplication::where('status', 'secretary_approved_final')
                        ->whereMonth('approved_at_secretary', now()->month)->whereYear('approved_at_secretary', now()->year)->count(),
            ],
        ];
    }

    /**
     * Get recent applications for operator.
     */
    private function getRecentApplicationsForOperator($userId): array
    {
        $electrician = ExElectricianRenewApplication::where('entry_by', $userId)
            ->latest()->take(5)->get()->map(fn($app) => [
                'type' => 'Electrician',
                'id' => $app->id,
                'certificate_number' => $app->old_certificate_number,
                'applicant_name' => $app->applicant_name_en,
                'status' => $app->status,
                'status_label' => $app->status_label,
                'status_badge_color' => $app->status_badge_color,
                'created_at' => $app->created_at,
            ]);

        $supervisor = ExSupervisorRenewApplication::where('entry_by', $userId)
            ->latest()->take(5)->get()->map(fn($app) => [
                'type' => 'Supervisor',
                'id' => $app->id,
                'certificate_number' => $app->old_certificate_number,
                'applicant_name' => $app->applicant_name_en,
                'status' => $app->status,
                'status_label' => $app->status_label,
                'status_badge_color' => $app->status_badge_color,
                'created_at' => $app->created_at,
            ]);

        $contractor = ExContractorRenewApplication::where('entry_by', $userId)
            ->latest()->take(5)->get()->map(fn($app) => [
                'type' => 'Contractor',
                'id' => $app->id,
                'certificate_number' => $app->old_certificate_number,
                'applicant_name' => $app->applicant_name_en,
                'status' => $app->status,
                'status_label' => $app->status_label,
                'status_badge_color' => $app->status_badge_color,
                'created_at' => $app->created_at,
            ]);

        return $electrician->merge($supervisor)->merge($contractor)
            ->sortByDesc('created_at')->take(5)->values()->toArray();
    }

    /**
     * Get pending applications for office assistant.
     */
    private function getPendingApplicationsForOfficeAssistant(): array
    {
        $electrician = ExElectricianRenewApplication::where('status', 'submitted_to_office_assistant')
            ->latest()->take(10)->get()->map(fn($app) => [
                'type' => 'Electrician',
                'id' => $app->id,
                'certificate_number' => $app->old_certificate_number,
                'applicant_name' => $app->applicant_name_en,
                'submitted_at' => $app->entry_at,
                'view_url' => route('ex-electrician.office-assistant.show', $app),
            ]);

        $supervisor = ExSupervisorRenewApplication::where('status', 'submitted_to_office_assistant')
            ->latest()->take(10)->get()->map(fn($app) => [
                'type' => 'Supervisor',
                'id' => $app->id,
                'certificate_number' => $app->old_certificate_number,
                'applicant_name' => $app->applicant_name_en,
                'submitted_at' => $app->entry_at,
                'view_url' => route('ex-supervisor.office-assistant.show', $app),
            ]);

        $contractor = ExContractorRenewApplication::where('status', 'submitted_to_office_assistant')
            ->latest()->take(10)->get()->map(fn($app) => [
                'type' => 'Contractor',
                'id' => $app->id,
                'certificate_number' => $app->old_certificate_number,
                'applicant_name' => $app->applicant_name_en,
                'submitted_at' => $app->entry_at,
                'view_url' => route('ex-contractor.office-assistant.show', $app),
            ]);

        return $electrician->merge($supervisor)->merge($contractor)
            ->sortByDesc('submitted_at')->take(10)->values()->toArray();
    }

    /**
     * Get pending applications for secretary.
     */
    private function getPendingApplicationsForSecretary(): array
    {
        $electrician = ExElectricianRenewApplication::where('status', 'submitted_to_secretary')
            ->latest()->take(10)->get()->map(fn($app) => [
                'type' => 'Electrician',
                'id' => $app->id,
                'certificate_number' => $app->old_certificate_number,
                'applicant_name' => $app->applicant_name_en,
                'submitted_at' => $app->verified_at_office_assistant,
                'view_url' => route('ex-electrician.secretary.show', $app),
            ]);

        $supervisor = ExSupervisorRenewApplication::where('status', 'submitted_to_secretary')
            ->latest()->take(10)->get()->map(fn($app) => [
                'type' => 'Supervisor',
                'id' => $app->id,
                'certificate_number' => $app->old_certificate_number,
                'applicant_name' => $app->applicant_name_en,
                'submitted_at' => $app->verified_at_office_assistant,
                'view_url' => route('ex-supervisor.secretary.show', $app),
            ]);

        $contractor = ExContractorRenewApplication::where('status', 'submitted_to_secretary')
            ->latest()->take(10)->get()->map(fn($app) => [
                'type' => 'Contractor',
                'id' => $app->id,
                'certificate_number' => $app->old_certificate_number,
                'applicant_name' => $app->applicant_name_en,
                'submitted_at' => $app->verified_at_office_assistant,
                'view_url' => route('ex-contractor.secretary.show', $app),
            ]);

        return $electrician->merge($supervisor)->merge($contractor)
            ->sortByDesc('submitted_at')->take(10)->values()->toArray();
    }

    /**
     * Get recent approvals for chairman.
     */
    private function getRecentApprovals(): array
    {
        $electrician = ExElectricianRenewApplication::where('status', 'secretary_approved_final')
            ->latest('approved_at_secretary')->take(10)->get()->map(fn($app) => [
                'type' => 'Electrician',
                'id' => $app->id,
                'certificate_number' => $app->old_certificate_number,
                'applicant_name' => $app->applicant_name_en,
                'approved_at' => $app->approved_at_secretary,
                'view_url' => route('ex-electrician.chairman.show', $app),
            ]);

        $supervisor = ExSupervisorRenewApplication::where('status', 'secretary_approved_final')
            ->latest('approved_at_secretary')->take(10)->get()->map(fn($app) => [
                'type' => 'Supervisor',
                'id' => $app->id,
                'certificate_number' => $app->old_certificate_number,
                'applicant_name' => $app->applicant_name_en,
                'approved_at' => $app->approved_at_secretary,
                'view_url' => route('ex-supervisor.chairman.show', $app),
            ]);

        $contractor = ExContractorRenewApplication::where('status', 'secretary_approved_final')
            ->latest('approved_at_secretary')->take(10)->get()->map(fn($app) => [
                'type' => 'Contractor',
                'id' => $app->id,
                'certificate_number' => $app->old_certificate_number,
                'applicant_name' => $app->applicant_name_en,
                'approved_at' => $app->approved_at_secretary,
                'view_url' => route('ex-contractor.chairman.show', $app),
            ]);

        return $electrician->merge($supervisor)->merge($contractor)
            ->sortByDesc('approved_at')->take(10)->values()->toArray();
    }

    /**
     * Get monthly processed count by office assistant.
     */
    private function getMonthlyProcessedByOfficeAssistant(): int
    {
        return ExElectricianRenewApplication::whereIn('status', ['submitted_to_secretary', 'office_assistant_rejected'])
            ->whereMonth('verified_at_office_assistant', now()->month)
            ->whereYear('verified_at_office_assistant', now()->year)->count() +
            ExSupervisorRenewApplication::whereIn('status', ['submitted_to_secretary', 'office_assistant_rejected'])
                ->whereMonth('verified_at_office_assistant', now()->month)
                ->whereYear('verified_at_office_assistant', now()->year)->count() +
            ExContractorRenewApplication::whereIn('status', ['submitted_to_secretary', 'office_assistant_rejected'])
                ->whereMonth('verified_at_office_assistant', now()->month)
                ->whereYear('verified_at_office_assistant', now()->year)->count();
    }
}
