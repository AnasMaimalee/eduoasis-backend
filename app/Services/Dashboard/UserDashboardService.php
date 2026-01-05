<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\ServiceRequest;
use App\Models\WalletTransaction;

class UserDashboardService
{
    public function summary(User $user): array
    {
        $requests = ServiceRequest::where('user_id', $user->id);

        return [
            'stats' => [
                'total_jobs'   => $requests->count(),
                'approved'     => (clone $requests)->where('status', 'approved')->count(),
                'rejected'     => (clone $requests)->where('status', 'rejected')->count(),
                'pending'      => (clone $requests)->whereIn('status', ['pending','processing'])->count(),
                'total_spent'  => WalletTransaction::where('user_id', $user->id)
                    ->where('type','debit')->sum('amount'),
            ],

            'spending_by_service' => ServiceRequest::with('service')
                ->where('user_id', $user->id)
                ->selectRaw('service_id, SUM(customer_price) as total')
                ->groupBy('service_id')
                ->get()
                ->map(fn ($r) => [
                    'service' => $r->service->name,
                    'amount'  => $r->total,
                ]),

            'recent_jobs' => ServiceRequest::with('service')
                ->where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn ($job) => [
                    'id'      => $job->id,
                    'service' => $job->service->name,
                    'status'  => $job->status,
                    'date'    => $job->created_at,
                ]),
        ];
    }
}
