<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('payments:check-overdue')
            ->daily()
            ->at('01:00')
            ->withoutOverlapping()
            ->emailOutputOnFailure('admin@supply4me.com');

        $schedule->command('stock:sync-levels')
            ->daily()
            ->at('02:00')
            ->withoutOverlapping();

        $schedule->command('reports:generate-daily')
            ->daily()
            ->at('03:00')
            ->withoutOverlapping();

        $schedule->command('invoice:generate-numbers', ['--count' => 50])
            ->daily()
            ->at('00:30')
            ->withoutOverlapping();

        $schedule->command('session:cleanup', ['--hours' => 24])
            ->daily()
            ->at('04:00')
            ->withoutOverlapping();

        $schedule->command('queue:work', ['--once'])
            ->everyMinute()
            ->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
