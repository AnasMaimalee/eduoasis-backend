<?php
namespace App\Http\Controllers\Api\ServiceProc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ServicePriceService;
use App\Models\Service;

class ServiceProcController extends Controller
{
    public function __construct(protected ServicePriceService $serviceService) {}

    // List all services
    public function index()
    {
        return response()->json($this->serviceService->listServices());
    }

    // Create a new service
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:services,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0'
        ]);

        $service = $this->serviceService->createService($request->all());

        return response()->json([
            'message' => 'Service created',
            'service' => $service
        ]);
    }

    // Get a service
    public function show(Service $service)
    {
        return response()->json($service);
    }

    // Update a service
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'sometimes|string|unique:services,name,' . $service->id,
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0'
        ]);

        $this->serviceService->updateService($service, $request->all());

        return response()->json([
            'message' => 'Service updated',
            'service' => $service->fresh()
        ]);
    }

    // Delete a service
    public function destroy(Service $service)
    {
        $this->serviceService->deleteService($service);

        return response()->json(['message' => 'Service deleted']);
    }
}
