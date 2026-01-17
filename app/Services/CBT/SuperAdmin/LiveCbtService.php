<?php

namespace App\Services\CBT\SuperAdmin;

use App\Repositories\CBT\SuperAdmin\LiveCbtRepository;

class LiveCbtService
{
    public function __construct(
        protected LiveCbtRepository $repo
    ) {}

    public function getLiveSessions()
    {
        return $this->repo->getLiveSessions();
    }
}
