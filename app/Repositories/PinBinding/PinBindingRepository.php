<?php

namespace App\Repositories\PinBinding;

use App\Http\Resources\PinBindingRequestResource;
use App\Models\JambPinBindingRequest;

class PinBindingRepository
{
    public function create(array $data)
    {
        return PinBindingRequestResource::create($data);
    }

    public function find(string $id)
    {
        return PinBindingRequestResource::findOrFail($id);
    }

    public function userRequests(string $userId)
    {
        return PinBindingRequestResource::where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function pending()
    {
        return PinBindingRequestResource::where('status', 'pending')->get();
    }

    public function allWithRelations()
    {
        return PinBindingRequestResource::with([
            'user',
            'service',
            'takenBy',
            'completedBy',
            'approvedBy',
            'rejectedBy',
        ])->latest()->get();
    }
}
