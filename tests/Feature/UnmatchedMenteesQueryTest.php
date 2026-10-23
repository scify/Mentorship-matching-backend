<?php

use App\StorageLayer\MenteeStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

/**
 * The query behind the daily mentees:notify-unmatched command: available
 * mentees without any session, created more than $months months ago.
 */
class UnmatchedMenteesQueryTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    public function test_it_returns_only_old_available_mentees_without_a_session() {
        $admin = $this->makeBackofficeUser(1, 'Admin');
        $old = $this->makeMenteeProfile($admin, ['created_at' => now()->subMonths(7)]);
        $recent = $this->makeMenteeProfile($admin, ['created_at' => now()->subMonths(5)]);
        $oldNotAvailable = $this->makeMenteeProfile($admin, ['created_at' => now()->subMonths(7), 'status_id' => 2]);
        $oldWithSession = $this->makeMenteeProfile($admin, ['created_at' => now()->subMonths(7)]);
        $mentor = $this->makeMentorProfile($admin, $this->makeCompany($admin, 'Company'));
        $this->makeSessionAtStatus($mentor, $oldWithSession, $admin, $admin, 1);

        $ids = (new MenteeStorage)->getUnmatchedMenteesCreatedSince(6)->pluck('id')->all();

        $this->assertContains($old, $ids);
        $this->assertNotContains($recent, $ids);
        $this->assertNotContains($oldNotAvailable, $ids);
        $this->assertNotContains($oldWithSession, $ids);
    }

    public function test_the_number_of_months_is_a_binding_not_part_of_the_sql() {
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query;
        });

        (new MenteeStorage)->getUnmatchedMenteesCreatedSince(6);

        $this->assertCount(1, $queries);
        $this->assertStringNotContainsString('6 MONTH', $queries[0]->sql);
        $this->assertContains(6, $queries[0]->bindings);
    }
}
