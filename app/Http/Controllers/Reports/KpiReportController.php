<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\ExElectricianRenewApplication;
use App\Models\ExSupervisorRenewApplication;
use App\Models\ExContractorRenewApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KpiReportController extends Controller
{
    /**
     * Display the Executive / Admin KPI Report & Analytics Dashboard.
     */
    public function kpi(Request $request)
    {
        $year = (int) $request->get('year', date('Y'));
        $permitType = $request->get('permit_type', 'all');
        $division = $request->get('division');

        $models = $this->resolveModels($permitType);

        // 1. Overall Totals
        $totals = [
            'total' => 0,
            'approved' => 0,
            'pending_oa' => 0,
            'pending_secretary' => 0,
            'rejected' => 0,
            'draft' => 0,
        ];

        foreach ($models as $model) {
            $base = $model::query()->whereYear('created_at', $year);
            if ($division) {
                $base->where('division', $division);
            }

            $totals['total'] += (clone $base)->count();
            $totals['approved'] += (clone $base)->where('status', 'secretary_approved_final')->count();
            $totals['pending_oa'] += (clone $base)->where('status', 'submitted_to_office_assistant')->count();
            $totals['pending_secretary'] += (clone $base)->where('status', 'submitted_to_secretary')->count();
            $totals['rejected'] += (clone $base)->whereIn('status', ['office_assistant_rejected', 'secretary_rejected'])->count();
            $totals['draft'] += (clone $base)->where('status', 'draft')->count();
        }

        $totals['total_pending'] = $totals['pending_oa'] + $totals['pending_secretary'];
        $totals['processed'] = $totals['approved'] + $totals['rejected'];

        // Rates
        $approvalRate = $totals['total'] > 0
            ? round(($totals['approved'] / $totals['total']) * 100, 1)
            : 0;
        $rejectionRate = $totals['total'] > 0
            ? round(($totals['rejected'] / $totals['total']) * 100, 1)
            : 0;

        // 2. Monthly Trend (Applications & Approvals across 12 months)
        $monthlyApplications = array_fill(1, 12, 0);
        $monthlyApprovals = array_fill(1, 12, 0);

        foreach ($models as $model) {
            // Applications by creation month
            $appQuery = $model::query()
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', $year);
            if ($division) $appQuery->where('division', $division);
            $apps = $appQuery->groupBy('month')->pluck('count', 'month');

            foreach ($apps as $m => $count) {
                $monthlyApplications[(int) $m] += $count;
            }

            // Approvals by secretary approved month
            $apprQuery = $model::query()
                ->selectRaw('MONTH(approved_at_secretary) as month, COUNT(*) as count')
                ->whereYear('approved_at_secretary', $year)
                ->where('status', 'secretary_approved_final');
            if ($division) $apprQuery->where('division', $division);
            $apprs = $apprQuery->groupBy('month')->pluck('count', 'month');

            foreach ($apprs as $m => $count) {
                $monthlyApprovals[(int) $m] += $count;
            }
        }

        // 3. Status Distribution
        $statusDistribution = [
            'draft' => $totals['draft'],
            'submitted_to_office_assistant' => $totals['pending_oa'],
            'submitted_to_secretary' => $totals['pending_secretary'],
            'office_assistant_rejected' => 0,
            'secretary_rejected' => 0,
            'secretary_approved_final' => $totals['approved'],
        ];

        foreach ($models as $model) {
            $base = $model::query()->whereYear('created_at', $year);
            if ($division) $base->where('division', $division);

            $statusDistribution['office_assistant_rejected'] += (clone $base)->where('status', 'office_assistant_rejected')->count();
            $statusDistribution['secretary_rejected'] += (clone $base)->where('status', 'secretary_rejected')->count();
        }

        // 4. Division Distribution
        $divisionDistribution = [];
        foreach ($models as $model) {
            $divQuery = $model::query()
                ->selectRaw('division, COUNT(*) as count')
                ->whereYear('created_at', $year)
                ->whereNotNull('division')
                ->where('division', '!=', '');
            $divs = $divQuery->groupBy('division')->pluck('count', 'division');

            foreach ($divs as $divName => $count) {
                $divisionDistribution[$divName] = ($divisionDistribution[$divName] ?? 0) + $count;
            }
        }
        arsort($divisionDistribution);

        // 5. Class Distribution
        $classDistribution = [];
        foreach ($models as $model) {
            $classQuery = $model::query()
                ->selectRaw('`class`, COUNT(*) as count')
                ->whereYear('created_at', $year)
                ->whereNotNull('class')
                ->where('class', '!=', '');
            if ($division) $classQuery->where('division', $division);
            $classes = $classQuery->groupBy('class')->pluck('count', 'class');

            foreach ($classes as $className => $count) {
                $classDistribution[$className] = ($classDistribution[$className] ?? 0) + $count;
            }
        }
        arsort($classDistribution);

        // 6. Pending Queue Aging Analysis
        $pendingAge = ['less_7' => 0, 'between_7_30' => 0, 'more_30' => 0];
        foreach ($models as $model) {
            $pendingQuery = $model::query()
                ->whereIn('status', ['submitted_to_office_assistant', 'submitted_to_secretary'])
                ->selectRaw('DATEDIFF(NOW(), created_at) as days_old');
            if ($division) $pendingQuery->where('division', $division);
            $pendingRecords = $pendingQuery->get();

            foreach ($pendingRecords as $rec) {
                $days = (int) $rec->days_old;
                if ($days < 7) {
                    $pendingAge['less_7']++;
                } elseif ($days <= 30) {
                    $pendingAge['between_7_30']++;
                } else {
                    $pendingAge['more_30']++;
                }
            }
        }

        // 7. Top 10 Operator Productivity
        $operatorProductivity = [];
        foreach ($models as $model) {
            $opQuery = $model::query()
                ->selectRaw('entry_by, COUNT(*) as count')
                ->whereYear('created_at', $year)
                ->whereNotNull('entry_by')
                ->groupBy('entry_by');
            if ($division) $opQuery->where('division', $division);
            $ops = $opQuery->pluck('count', 'entry_by');

            foreach ($ops as $userId => $count) {
                $operatorProductivity[$userId] = ($operatorProductivity[$userId] ?? 0) + $count;
            }
        }
        arsort($operatorProductivity);
        $topOperatorIds = array_slice(array_keys($operatorProductivity), 0, 10);
        $operators = User::whereIn('id', $topOperatorIds)->get()->keyBy('id');

        $topOperators = [];
        foreach ($topOperatorIds as $opId) {
            $topOperators[] = [
                'user' => $operators->get($opId),
                'count' => $operatorProductivity[$opId],
            ];
        }

        $availableYears = range(date('Y'), 2020);
        $divisionsList = ['Dhaka', 'Chittagong', 'Rajshahi', 'Khulna', 'Barisal', 'Sylhet', 'Rangpur', 'Mymensingh'];

        return view('reports.kpi', compact(
            'totals',
            'approvalRate',
            'rejectionRate',
            'monthlyApplications',
            'monthlyApprovals',
            'statusDistribution',
            'divisionDistribution',
            'classDistribution',
            'pendingAge',
            'topOperators',
            'year',
            'permitType',
            'division',
            'availableYears',
            'divisionsList'
        ));
    }

    /**
     * Operator Performance Report (Role-Specific for Data Entry Operator).
     */
    public function operatorReport(Request $request)
    {
        $user = Auth::user();
        $targetUserId = ($user->hasRole(['super_admin', 'system_admin']) && $request->filled('user_id'))
            ? (int) $request->get('user_id')
            : $user->id;

        $targetUser = User::find($targetUserId) ?? $user;

        $permitType = $request->get('permit_type', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $models = $this->resolveModels($permitType);

        $stats = [
            'total' => 0,
            'today' => 0,
            'this_month' => 0,
            'draft' => 0,
            'submitted' => 0,
            'approved' => 0,
            'rejected' => 0,
        ];

        $recentEntries = collect();

        foreach ($models as $typeKey => $model) {
            $query = $model::query()->where('entry_by', $targetUserId);

            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            $stats['total'] += (clone $query)->count();
            $stats['today'] += (clone $query)->whereDate('created_at', today())->count();
            $stats['this_month'] += (clone $query)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
            $stats['draft'] += (clone $query)->where('status', 'draft')->count();
            $stats['submitted'] += (clone $query)->whereIn('status', ['submitted_to_office_assistant', 'submitted_to_secretary'])->count();
            $stats['approved'] += (clone $query)->where('status', 'secretary_approved_final')->count();
            $stats['rejected'] += (clone $query)->whereIn('status', ['office_assistant_rejected', 'secretary_rejected'])->count();

            // Fetch some recent entries
            $items = (clone $query)->latest()->limit(10)->get()->map(function ($item) use ($typeKey) {
                $item->permit_type_label = ucfirst($typeKey);
                return $item;
            });
            $recentEntries = $recentEntries->concat($items);
        }

        $recentEntries = $recentEntries->sortByDesc('created_at')->take(20);

        // Daily volume for the last 14 days
        $dailyVolume = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $dailyVolume[$d] = 0;
        }

        foreach ($models as $model) {
            $days = $model::query()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('entry_by', $targetUserId)
                ->where('created_at', '>=', now()->subDays(14)->startOfDay())
                ->groupBy('date')
                ->pluck('count', 'date');

            foreach ($days as $dateStr => $c) {
                if (isset($dailyVolume[$dateStr])) {
                    $dailyVolume[$dateStr] += $c;
                }
            }
        }

        return view('reports.operator', compact(
            'stats',
            'recentEntries',
            'dailyVolume',
            'targetUser',
            'permitType',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Office Assistant Performance Report (Role-Specific for Office Assistant).
     */
    public function officeAssistantReport(Request $request)
    {
        $user = Auth::user();
        $targetUserId = ($user->hasRole(['super_admin', 'system_admin']) && $request->filled('user_id'))
            ? (int) $request->get('user_id')
            : $user->id;

        $targetUser = User::find($targetUserId) ?? $user;

        $permitType = $request->get('permit_type', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $models = $this->resolveModels($permitType);

        $stats = [
            'verified_total' => 0,
            'verified_today' => 0,
            'rejected_total' => 0,
            'rejected_today' => 0,
            'pending_now' => 0,
        ];

        $byType = [
            'electrician' => ['verified' => 0, 'rejected' => 0, 'pending' => 0],
            'supervisor' => ['verified' => 0, 'rejected' => 0, 'pending' => 0],
            'contractor' => ['verified' => 0, 'rejected' => 0, 'pending' => 0],
        ];

        foreach ($models as $key => $model) {
            // Current pending in system
            $pendingCount = $model::where('status', 'submitted_to_office_assistant')->count();
            $stats['pending_now'] += $pendingCount;
            $byType[$key]['pending'] = $pendingCount;

            // Verified
            $verQuery = $model::query()->where('verified_by_office_assistant', $targetUserId);
            if ($dateFrom) $verQuery->whereDate('verified_at_office_assistant', '>=', $dateFrom);
            if ($dateTo) $verQuery->whereDate('verified_at_office_assistant', '<=', $dateTo);

            $verTotal = (clone $verQuery)->count();
            $verToday = (clone $verQuery)->whereDate('verified_at_office_assistant', today())->count();

            $stats['verified_total'] += $verTotal;
            $stats['verified_today'] += $verToday;
            $byType[$key]['verified'] = $verTotal;

            // Rejected
            $rejQuery = $model::query()->where('rejected_by', $targetUserId)->where('status', 'office_assistant_rejected');
            if ($dateFrom) $rejQuery->whereDate('rejected_at', '>=', $dateFrom);
            if ($dateTo) $rejQuery->whereDate('rejected_at', '<=', $dateTo);

            $rejTotal = (clone $rejQuery)->count();
            $rejToday = (clone $rejQuery)->whereDate('rejected_at', today())->count();

            $stats['rejected_total'] += $rejTotal;
            $stats['rejected_today'] += $rejToday;
            $byType[$key]['rejected'] = $rejTotal;
        }

        $totalProcessed = $stats['verified_total'] + $stats['rejected_total'];
        $verificationRate = $totalProcessed > 0
            ? round(($stats['verified_total'] / $totalProcessed) * 100, 1)
            : 0;

        return view('reports.office_assistant', compact(
            'stats',
            'byType',
            'verificationRate',
            'targetUser',
            'permitType',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Resolve model classes based on permit type.
     */
    private function resolveModels(string $permitType): array
    {
        return match ($permitType) {
            'electrician' => ['electrician' => ExElectricianRenewApplication::class],
            'supervisor' => ['supervisor' => ExSupervisorRenewApplication::class],
            'contractor' => ['contractor' => ExContractorRenewApplication::class],
            default => [
                'electrician' => ExElectricianRenewApplication::class,
                'supervisor' => ExSupervisorRenewApplication::class,
                'contractor' => ExContractorRenewApplication::class,
            ],
        };
    }
}
