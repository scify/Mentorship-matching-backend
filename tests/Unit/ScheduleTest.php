<?php

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

/**
 * Supervisor runs the queue worker (see DEPLOYMENT.md). A second worker from
 * the scheduler would keep old code in memory after a deploy.
 */
class ScheduleTest extends TestCase {
    public function test_the_schedule_runs_the_daily_commands_and_no_queue_worker() {
        $commands = array_map(
            fn (Event $event) => preg_replace("/^.*'artisan' /", '', (string) $event->command) . ' @ ' . $event->expression,
            app(Schedule::class)->events(),
        );

        $this->assertEqualsCanonicalizing(['mentees:notify-unmatched @ 0 0 * * *', 'email:follow-up @ 0 0 * * *'], $commands);
    }
}
