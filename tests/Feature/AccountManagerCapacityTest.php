<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

/**
 * An account manager sets their own capacity on their profile page; an admin
 * may set anyone's. The change is a POST.
 */
class AccountManagerCapacityTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    public function test_an_account_manager_sets_their_own_capacity() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');

        $this->actingAs($accountManager)
            ->post("/user/{$accountManager->id}/editUserCapacity", ['capacity' => 7])
            ->assertOk();

        $this->assertSame(7, $this->capacity($accountManager->id));
    }

    public function test_an_admin_sets_the_capacity_of_an_account_manager() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');

        $this->actingAs($this->makeBackofficeUser(1, 'Admin'))
            ->post("/user/{$accountManager->id}/editUserCapacity", ['capacity' => 4])
            ->assertOk();

        $this->assertSame(4, $this->capacity($accountManager->id));
    }

    public function test_another_account_manager_or_a_matcher_cannot_set_it() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');

        foreach ([$this->makeBackofficeUser(3, 'Other'), $this->makeBackofficeUser(2, 'Matcher')] as $user) {
            $this->actingAs($user)->post("/user/{$accountManager->id}/editUserCapacity", ['capacity' => 99]);
            $this->actingAs($user)->post("/user/{$user->id}/editUserCapacity", ['capacity' => 99, 'user_id' => $accountManager->id]);
        }

        $this->assertNull($this->capacity($accountManager->id));
    }

    public function test_a_get_request_changes_nothing() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');

        $this->actingAs($accountManager)
            ->get("/user/{$accountManager->id}/editUserCapacity?user_id={$accountManager->id}&capacity=9")
            ->assertStatus(405);

        $this->assertNull($this->capacity($accountManager->id));
    }

    public function test_a_negative_or_non_numeric_capacity_is_refused() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');
        $this->actingAs($accountManager);

        $this->postJson("/user/{$accountManager->id}/editUserCapacity", ['capacity' => -1])->assertStatus(422);
        $this->postJson("/user/{$accountManager->id}/editUserCapacity", ['capacity' => 'many'])->assertStatus(422);

        $this->assertNull($this->capacity($accountManager->id));
    }

    private function capacity(int $userId): ?int {
        $capacity = DB::table('account_manager_capacity')->where('account_manager_id', $userId)->value('capacity');

        return $capacity === null ? null : (int) $capacity;
    }
}
