<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

/**
 * On their own profile an account manager may pick a company that has no
 * account manager, as the form offers. Only an admin moves a company from one
 * account manager to another.
 */
class UserCompanyAssignmentTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    public function test_an_account_manager_picks_a_free_company() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');
        $companyId = $this->makeFreeCompany('Free');

        $this->actingAs($accountManager)->post("/user/{$accountManager->id}/edit", $this->profile($accountManager, $companyId));

        $this->assertSame($accountManager->id, $this->accountManagerOf($companyId));
    }

    public function test_an_account_manager_cannot_take_a_company_from_another() {
        $owner = $this->makeBackofficeUser(3, 'Owner');
        $companyId = $this->makeCompany($owner, 'Taken');
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');

        $this->actingAs($accountManager)->post("/user/{$accountManager->id}/edit", $this->profile($accountManager, $companyId));

        $this->assertSame($owner->id, $this->accountManagerOf($companyId));
    }

    public function test_a_matcher_cannot_become_the_account_manager_of_a_company() {
        $matcher = $this->makeBackofficeUser(2, 'Matcher');
        $companyId = $this->makeFreeCompany('Free');

        $this->actingAs($matcher)->post("/user/{$matcher->id}/edit", $this->profile($matcher, $companyId));

        $this->assertNull($this->accountManagerOf($companyId));
    }

    public function test_an_admin_moves_a_company_to_another_account_manager() {
        $owner = $this->makeBackofficeUser(3, 'Owner');
        $companyId = $this->makeCompany($owner, 'Taken');
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');

        $this->actingAs($this->makeBackofficeUser(1, 'Admin'))
            ->post("/user/{$accountManager->id}/edit", $this->profile($accountManager, $companyId) + ['user_roles' => [['id' => 3]]]);

        $this->assertSame($accountManager->id, $this->accountManagerOf($companyId));
    }

    private function profile($user, int $companyId): array {
        return [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'usericon' => DB::table('user_icon')->value('title'),
            'company_id' => $companyId,
            'capacity' => 3,
            'password' => '',
            'passwordconfirm' => '',
        ];
    }

    private function makeFreeCompany(string $name): int {
        return (int) DB::table('company')->insertGetId(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function accountManagerOf(int $companyId): ?int {
        $id = DB::table('company')->where('id', $companyId)->value('account_manager_id');

        return $id === null ? null : (int) $id;
    }
}
