<?php

namespace App\Repositories\JambPinBinding;

use App\Http\Resources\JambPinBindingRequestResource;
use App\Models\JambPinBindingRequest;

class JambPinBindingRepository
{
    public function create(array $data)
    {
        return JambPinBindingRequest::create($data);
    }

    public function find(string $id)
    {
        return JambPinBindingRequest::findOrFail($id);
    }

    public function userRequests(string $userId)
    {
        return JambPinBindingRequest::where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function pending()
    {
        return JambPinBindingRequest::where('status', 'pending')->get();
    }

    public function allWithRelations()
    {
        return JambPinBindingRequest::with([
            'user',
            'service',
            'takenBy',
            'completedBy',
            'approvedBy',
            'rejectedBy',
        ])->latest()->get();
    }
}
