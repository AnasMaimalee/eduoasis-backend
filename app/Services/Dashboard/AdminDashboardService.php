<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\ServiceRequest;
use App\Models\WalletTransaction;

class AdminDashboardService
{
    public function summary(User $admin): array
    {
        if (! $admin->hasRole('administrator')) {
            abort(403);
        }

        $jobs = ServiceRequest::where('completed_by', $admin->id);

        $processed = $jobs->count();
        $approved  = (clone $jobs)->where('status','approved')->count();
        $rejected  = (clone $jobs)->where('status','rejected')->count();

        return [
            'stats' => [
                'processed_jobs' => $processed,
                'approved_jobs'  => $approved,
                'rejected_jobs'  => $rejected,
                'performance'    => $processed
                    ? round(($approved/$processed)*100,2).'%' : '0%',
                'earnings'       => WalletTransaction::where('user_id',$admin->id)
                    ->where('type','credit')->sum('amount'),
            ],

            'jobs_by_service' => ServiceRequest::with('service')
                ->where('completed_by',$admin->id)
                ->selectRaw('service_id, COUNT(*) as total')
                ->groupBy('service_id')
                ->get()
                ->map(fn ($j) => [
                    'service' => $j->service->name,
                    'jobs'    => $j->total,
                ]),

            'fraud_flags' => [
                'high_rejection_rate' => $processed > 20 && ($rejected/$processed) > 0.5,
                'too_fast_processing' => false, // extend with timestamps
            ],
        ];
    }
}
