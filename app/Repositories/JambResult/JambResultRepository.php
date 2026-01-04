<?php

namespace App\Repositories\JambResult;

use App\Models\JambResultRequest;

class JambResultRepository
{
    protected ?JambResultRequest $model = null;

    public function __construct(JambResultRequest $model)
    {
        $this->model = $model;
    }

    public function pendingRequests()
    {
        return JambResultRequest::where('status', 'pending')->get();
    }

    /**
     * Optionally, for a specific admin
     */
    public function adminPendingRequests($adminId)
    {
        return JambResultRequest::where('status', 'pending')
            ->where('taken_by', null)
            ->get();
    }
    public function create(array $data)
    {
        return JambResultRequest::create($data);
    }

    public function find(string $id)
    {
        return JambResultRequest::findOrFail($id);
    }

    public function userRequests(string $userId)
    {
        return JambResultRequest::where('user_id', $userId)->latest()->get();
    }

    public function pending()
    {
        return JambResultRequest::where('status', 'pending')->get();
    }

    public function takenBy(string $adminId)
    {
        return JambResultRequest::where('taken_by', $adminId)->get();
    }
    public function allWithRelations()
    {
        return $this->model
            ->with([
                'user',
                'service',
                'takenBy',
                'completedBy',
                'approvedBy',
                'rejectedBy',
            ])
            ->latest()
            ->get();
    }
}
