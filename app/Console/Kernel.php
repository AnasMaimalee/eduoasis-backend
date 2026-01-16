<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\CBT\ExamService;

class Kernel extends ConsoleKernel
{
    /**
     * Register Artisan commands.
     */
    protected $commands = [
        \App\Console\Commands\AutoSubmitExpiredExams::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // 1️⃣ Submit exams whose time has ended
        $schedule->command('cbt:auto-submit-expired-exams')
            ->everyMinute()
            ->withoutOverlapping();

        // 2️⃣ Submit inactive exams (refresh / tab close / disconnect)
        $schedule->call(function () {
            app(ExamService::class)->autoSubmitInactive();
        })
            ->everyMinute()
            ->withoutOverlapping();
    }
}
