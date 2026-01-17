<?php

namespace App\Repositories\CBT\SuperAdmin;


use App\Models\CbtSetting;

interface CbtSettingRepositoryInterface
{
    public function first(): ?CbtSetting;
    public function updateOrCreate(array $data): CbtSetting;
}
