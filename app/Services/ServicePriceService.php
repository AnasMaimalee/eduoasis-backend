<?php
namespace App\Services;

use App\Repositories\ServiceRepository;
use App\Models\Service;

class ServicePriceService
{
    public function __construct(protected ServiceRepository $serviceRepo) {}

    public function listServices()
    {
        return $this->serviceRepo->all();
    }

    public function createService(array $data): Service
    {
        return $this->serviceRepo->create($data);
    }

    public function getService(string $id): Service
    {
        return $this->serviceRepo->find($id);
    }

    public function updateService(Service $service, array $data): bool
    {
        return $this->serviceRepo->update($service, $data);
    }

    public function deleteService(Service $service): bool
    {
        return $this->serviceRepo->delete($service);
    }
}
