<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

/**
 * A session history row may be deleted by the staff user who wrote it or by
 * an admin, as the session modal shows the delete button.
 */
class SessionHistoryDeleteTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    public function test_the_author_deletes_their_own_history_row() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');
        $rowId = $this->makeHistoryRow($accountManager->id);

        $this->actingAs($accountManager)->post('/delete-history-item', ['sessionHistoryId' => $rowId]);

        $this->assertTrue($this->isDeleted($rowId));
    }

    public function test_an_admin_deletes_any_history_row() {
        $rowId = $this->makeHistoryRow($this->makeBackofficeUser(3, 'AccMan')->id);

        $this->actingAs($this->makeBackofficeUser(1, 'Admin'))->post('/delete-history-item', ['sessionHistoryId' => $rowId]);

        $this->assertTrue($this->isDeleted($rowId));
    }

    public function test_another_staff_user_cannot_delete_the_row() {
        $rowId = $this->makeHistoryRow($this->makeBackofficeUser(3, 'AccMan')->id);

        foreach ([3 => 'OtherAccMan', 2 => 'Matcher'] as $roleId => $label) {
            $response = $this->actingAs($this->makeBackofficeUser($roleId, $label))
                ->post('/delete-history-item', ['sessionHistoryId' => $rowId]);
            $this->assertSame(2, json_decode($response->getContent())->status);
        }

        $this->assertFalse($this->isDeleted($rowId));
    }

    public function test_an_unknown_id_fails_without_an_error_page() {
        $response = $this->actingAs($this->makeBackofficeUser(1, 'Admin'))
            ->post('/delete-history-item', ['sessionHistoryId' => 999999]);

        $response->assertOk();
        $this->assertSame(2, json_decode($response->getContent())->status);
    }

    private function makeHistoryRow(int $authorId): int {
        $admin = $this->makeBackofficeUser(1, 'Creator');
        $session = $this->makeSessionAtStatus(
            $this->makeMentorProfile($admin, $this->makeCompany($admin, 'Company')),
            $this->makeMenteeProfile($admin),
            $admin, $admin, 5,
        );

        return (int) DB::table('mentorship_session_history')->insertGetId([
            'mentorship_session_id' => $session->id, 'user_id' => $authorId, 'status_id' => 5,
            'comment' => '', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function isDeleted(int $rowId): bool {
        return DB::table('mentorship_session_history')->where('id', $rowId)->whereNotNull('deleted_at')->exists();
    }
}
