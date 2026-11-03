<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\SendFollowUpEmails',
        'App\Console\Commands\HandleUnmatchedMentees',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('mentees:notify-unmatched')->daily();
        $schedule->command('email:follow-up')->daily();
        // $schedule->command('command:hello')->everyMinute();
        // Supervisor runs the queue worker; see DEPLOYMENT.md. Shared hosting
        // without Supervisor sets QUEUE_SCHEDULED_WORKER, and cron's
        // schedule:run starts a worker that exits when the queue is empty.
        // The host may kill it; the short lock expiry and the retries keep
        // the queue moving after a kill.
        if (config('queue.scheduled_worker')) {
            $schedule->command('queue:work --stop-when-empty --tries=3 --max-time=60')
                ->everyTwoMinutes()
                ->withoutOverlapping(5);
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
