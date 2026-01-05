<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\ServiceRequest;
use App\Models\WalletTransaction;

class SuperAdminDashboardService
{
    public function summary(): array
    {
        $jobs = ServiceRequest::query();

        return [
            'overview' => [
                'total_jobs'       => $jobs->count(),
                'approved_jobs'    => (clone $jobs)->where('status','approved')->count(),
                'rejected_jobs'    => (clone $jobs)->where('status','rejected')->count(),
                'total_revenue'    => WalletTransaction::where('type','debit')->sum('amount'),
                'admin_payouts'    => WalletTransaction::where('type','credit')
                    ->whereHas('user.roles', fn ($q)=>$q->where('name','administrator'))
                    ->sum('amount'),
                'platform_profit'  => ServiceRequest::sum('platform_profit'),
            ],

            'jobs_by_service' => ServiceRequest::with('service')
                ->selectRaw('service_id, COUNT(*) as total')
                ->groupBy('service_id')
                ->get()
                ->map(fn ($j)=>[
                    'service' => $j->service->name,
                    'jobs'    => $j->total,
                ]),

            'admin_leaderboard' => User::role('administrator')->get()->map(function ($admin) {
                $count = ServiceRequest::where('completed_by',$admin->id)->count();
                return [
                    'admin' => $admin->name,
                    'jobs'  => $count,
                    'earnings' => WalletTransaction::where('user_id',$admin->id)
                        ->where('type','credit')->sum('amount'),
                ];
            })->sortByDesc('jobs')->values(),

            'fraud_indicators' => [
                'admins_with_zero_approvals' =>
                    User::role('administrator')->whereDoesntHave('completedJobs')->count(),
            ],
        ];
    }
}
