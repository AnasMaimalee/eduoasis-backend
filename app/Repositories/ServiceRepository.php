<?php
namespace App\Repositories;

use App\Models\Service;

class ServiceRepository
{
    public function all()
    {
        return Service::all();
    }

    public function find(string $id)
    {
        return Service::findOrFail($id);
    }

    public function create(array $data)
    {
        return Service::create($data);
    }

    public function update(Service $service, array $data)
    {
        return $service->update($data);
    }

    public function delete(Service $service)
    {
        return $service->delete();
    }
}
