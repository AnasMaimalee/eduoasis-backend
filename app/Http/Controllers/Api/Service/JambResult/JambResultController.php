<?php

namespace App\Http\Controllers\Api\Service\JambResult;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JambResult\JambResultService;
use App\Http\Resources\JambResultRequestResource;
class JambResultController extends Controller
{
    public function __construct(
        protected JambResultService $service
    ) {}

    /**
     * ======================
     * USER
     * ======================
     */

// User sees only his own requests
    public function my()
    {
        return response()->json(
            $this->service->my(auth()->user())
        );
    }

// User submits request
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone_number' => 'nullable|string',
            'registration_number' => 'nullable|string',
        ]);

        return response()->json(
            $this->service->submit(auth()->user(), $request->all())
        );
    }

    /**
     * ======================
     * ADMIN / SUPER ADMIN
     * ======================
     */

// 🔥 index = admin overview
    public function index()
    {
        return response()->json(
            $this->service->index(auth()->user())
        );
    }

// Only unassigned jobs
    public function pending()
    {
        return JambResultRequestResource::collection(
            $this->service->pending()
        );
    }

    /**
     * ======================
     * ADMINISTRATOR (WORKER)
     * ======================
     */

// Take (lock) a job
    public function take(string $id)
    {
        return response()->json(
            $this->service->take($id, auth()->user())
        );
    }

// Complete job
    public function complete(Request $request, string $jambRequest)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'price' => 'required|numeric|min:0',
        ]);

        $path = $request->file('file')->store('jamb-results', 'public');

        return response()->json(
            $this->service->complete(
                $jambRequest,
                $path,
                auth()->user()
            )
        );
    }

    public function approve(string $id)
    {
        return response()->json(
            $this->service->approve($id, auth()->user())
        );
    }

    public function reject(Request $request, string $id)
    {
        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        return response()->json(
            $this->service->reject(
                $id,
                $request->reason,
                auth()->user()
            )
        );
    }

    public function all()
    {
        return response()->json(
            $this->service->all()
        );
    }
}
