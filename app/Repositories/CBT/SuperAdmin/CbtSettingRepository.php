<?php

namespace App\Repositories\CBT\SuperAdmin;

use App\Models\CbtSetting;
use Illuminate\Support\Str;

class CbtSettingRepository
{
    private const GLOBAL_ID = 'cbt-settings-global';

    public function get(): CbtSetting
    {
        return CbtSetting::firstOrCreate(
            ['id' => self::GLOBAL_ID],
            [
                'subjects_count' => 4,
                'questions_per_subject' => 15,
                'duration_minutes' => 120,
                'exam_fee' => 0,
            ]
        );
    }

    public function update(array $data): CbtSetting
    {
        $settings = $this->get();
        $settings->update($data);
        return $settings->fresh();
    }
}
