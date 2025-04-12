<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Task;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            Task::where('status', 'deleted')
                ->where('deleted_at', '<=', now()->subDays(30))
                ->delete();
        })->daily();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
