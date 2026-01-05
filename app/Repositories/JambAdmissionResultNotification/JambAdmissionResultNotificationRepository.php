<?php

namespace App\Repositories\JambAdmissionResultNotification;

use App\Models\JambAdmissionResultNotificationRequest;

class JambAdmissionResultNotificationRepository
{
    public function create(array $data)
    {
        return JambAdmissionResultNotificationRequest::create($data);
    }

    public function find(string $id)
    {
        return JambAdmissionResultNotificationRequest::findOrFail($id);
    }

    public function userRequests(string $userId)
    {
        return JambAdmissionResultNotificationRequest::where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function pending()
    {
        return JambAdmissionResultNotificationRequest::where('status', 'pending')->get();
    }

    public function allWithRelations()
    {
        return JambAdmissionResultNotificationRequest::with([
            'user',
            'service',
            'takenBy',
            'completedBy',
            'approvedBy',
            'rejectedBy',
        ])->latest()->get();
    }
}
