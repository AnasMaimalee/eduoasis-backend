<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\ServiceRequest;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class SuperAdminDashboardService
{
    public function summary(): array
    {
        $jobs = ServiceRequest::query();

        // List all your actual request tables here
        $requestTables = [
            'jamb_result_requests',
            'jamb_admission_letter_requests',
            'jamb_admission_status_requests',
            'jamb_upload_status_requests',
            'jamb_admission_result_notification_requests',
            // Add any new ones here in the future
        ];

        // Build union query to count completed jobs across all services
        $completedJobsSubquery = null;
        foreach ($requestTables as $table) {
            $query = DB::table($table)->select('completed_by')->whereNotNull('completed_by');

            if ($completedJobsSubquery === null) {
                $completedJobsSubquery = $query;
            } else {
                $completedJobsSubquery->unionAll($query);
            }
        }

        // Get admin IDs who have completed at least one job
        $adminsWithJobs = DB::table(DB::raw("({$completedJobsSubquery->toSql()}) as completed"))
            ->mergeBindings($completedJobsSubquery)
            ->distinct()
            ->pluck('completed_by');

        // Total admins vs admins with zero jobs
        $totalAdmins = User::role('administrator')->count();
        $adminsWithZeroJobs = $totalAdmins - $adminsWithJobs->count();

        return [
            'overview' => [
                'total_jobs'       => $jobs->count(),
                'approved_jobs'    => (clone $jobs)->where('status', 'approved')->count(),
                'rejected_jobs'    => (clone $jobs)->where('status', 'rejected')->count(),
                'total_revenue'    => WalletTransaction::where('type', 'debit')->sum('amount'),
                'admin_payouts'    => WalletTransaction::where('type', 'credit')
                    ->whereHas('user.roles', fn ($q) => $q->where('name', 'administrator'))
                    ->sum('amount'),
                'platform_profit'  => ServiceRequest::sum('platform_profit'),
            ],

            'jobs_by_service' => ServiceRequest::with('service')
                ->selectRaw('service_id, COUNT(*) as total')
                ->groupBy('service_id')
                ->get()
                ->map(fn ($j) => [
                    'service' => $j->service?->name ?? 'Unknown',
                    'jobs'    => $j->total,
                ]),

            'admin_leaderboard' => User::role('administrator')->get()->map(function ($admin) use ($requestTables) {
                // Count completed jobs across all tables
                $completedCount = 0;
                foreach ($requestTables as $table) {
                    $completedCount += DB::table($table)
                        ->where('completed_by', $admin->id)
                        ->count();
                }

                return [
                    'admin'     => $admin->name,
                    'email'     => $admin->email,
                    'jobs'      => $completedCount,
                    'earnings'  => WalletTransaction::where('user_id', $admin->id)
                        ->where('type', 'credit')
                        ->sum('amount'),
                ];
            })->sortByDesc('jobs')->values(),

            'fraud_indicators' => [
                'admins_with_zero_approvals' => $adminsWithZeroJobs,
                'total_admins'               => $totalAdmins,
            ],
        ];
    }
}
