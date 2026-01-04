<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ServiceRequestService;
use App\Models\Service;

class ServiceRequestController extends Controller
{
    public function __construct(protected ServiceRequestService $serviceRequestService){}

    public function request(Request $request)
    {
        $request->validate([
            'service_id' => 'required|uuid|exists:services,id',
        ]);

        $service = Service::findOrFail($request->service_id);
        $user = auth()->user();

        $serviceRequest = $this->serviceRequestService->requestService($user, $service);

        return response()->json([
            'message' => "Service {$service->name} requested successfully",
            'service_request' => $serviceRequest
        ]);
    }
}
