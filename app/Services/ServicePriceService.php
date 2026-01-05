<?php

namespace App\Services;

use App\Repositories\ServiceRepository;
use App\Models\Service;

class ServicePriceService
{
    public function __construct(
        protected ServiceRepository $serviceRepo
    ) {}

    /**
     * Update customer price and/or admin payout for a service
     */
    public function updatePrices(
        string $serviceId,
        ?float $customerPrice = null,
        ?float $adminPayout = null,
        ?string $description = null
    ): Service {
        // 1️⃣ Find service
        $service = $this->serviceRepo->find($serviceId);

        // 2️⃣ Delegate update logic
        return $this->serviceRepo->updatePrices(
            $service,
            $description,
            $customerPrice,
            $adminPayout
        );
    }
}
