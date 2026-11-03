<?php

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

/**
 * Supervisor runs the queue worker (see DEPLOYMENT.md). A second worker from
 * the scheduler would keep old code in memory after a deploy. A host without
 * Supervisor sets QUEUE_SCHEDULED_WORKER, and cron starts a short-lived worker.
 */
class ScheduleTest extends TestCase {
    public function test_the_schedule_runs_the_daily_commands_and_no_queue_worker() {
        $this->assertEqualsCanonicalizing(['mentees:notify-unmatched @ 0 0 * * *', 'email:follow-up @ 0 0 * * *'], $this->scheduledCommands());
    }

    public function test_the_schedule_runs_a_short_lived_queue_worker_when_the_host_has_no_supervisor() {
        config(['queue.scheduled_worker' => true]);

        $this->assertEqualsCanonicalizing([
            'mentees:notify-unmatched @ 0 0 * * *',
            'email:follow-up @ 0 0 * * *',
            'queue:work --stop-when-empty --tries=3 --max-time=60 @ */2 * * * *',
        ], $this->scheduledCommands());

        $worker = collect(app(Schedule::class)->events())
            ->first(fn (Event $event) => str_contains((string) $event->command, 'queue:work'));
        // A killed worker never releases its lock: a short expiry keeps the queue moving.
        $this->assertTrue($worker->withoutOverlapping);
        $this->assertSame(5, $worker->expiresAt);
    }

    /** @return string[] */
    private function scheduledCommands(): array {
        return array_map(
            fn (Event $event) => preg_replace("/^.*'artisan' /", '', (string) $event->command) . ' @ ' . $event->expression,
            app(Schedule::class)->events(),
        );
    }
}
