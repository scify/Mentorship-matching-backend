<?php

use App\BusinessLogicLayer\managers\UserAccessManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

/**
 * Characterizes the role checks every route middleware delegates to.
 * Role checks read the user's roles on every check, so a role change takes
 * effect at once.
 */
class UserAccessManagerTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    private UserAccessManager $accessManager;

    protected function setUp(): void {
        parent::setUp();
        $this->accessManager = new UserAccessManager;
    }

    public function test_admin_has_every_admin_only_access() {
        $admin = $this->makeBackofficeUser($this->accessManager->ADMINISTRATOR_ROLE_ID, 'Admin');

        $this->assertTrue($this->accessManager->userIsAdmin($admin));
        $this->assertTrue($this->accessManager->userHasAccessToCRUDSystemUsers($admin));
        $this->assertTrue($this->accessManager->userHasAccessToCRUDMentorsAndMentees($admin));
        $this->assertTrue($this->accessManager->userHasAccessToEditMentorsAndMentees($admin));
        $this->assertTrue($this->accessManager->userHasAccessToCRUDCompanies($admin));
        $this->assertTrue($this->accessManager->userHasAccessToCRUDMentorshipSessions($admin));
        $this->assertFalse($this->accessManager->userIsMatcher($admin));
        $this->assertFalse($this->accessManager->userIsAccountManager($admin));
    }

    public function test_matcher_has_no_admin_or_account_manager_access() {
        $matcher = $this->makeBackofficeUser($this->accessManager->MATCHER_ROLE_ID, 'Matcher');

        $this->assertTrue($this->accessManager->userIsMatcher($matcher));
        $this->assertFalse($this->accessManager->userIsAdmin($matcher));
        $this->assertFalse($this->accessManager->userHasAccessToCRUDSystemUsers($matcher));
        $this->assertFalse($this->accessManager->userHasAccessToEditMentorsAndMentees($matcher));
        $this->assertFalse($this->accessManager->userHasAccessToOnlyEditStatusForMentorshipSessions($matcher));
    }

    public function test_account_manager_can_edit_profiles_and_session_status_but_is_not_admin() {
        $accountManager = $this->makeBackofficeUser($this->accessManager->ACCOUNT_MANAGER_ROLE_ID, 'Accman');

        $this->assertTrue($this->accessManager->userIsAccountManager($accountManager));
        $this->assertTrue($this->accessManager->userHasAccessToEditMentorsAndMentees($accountManager));
        $this->assertTrue($this->accessManager->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees($accountManager));
        $this->assertTrue($this->accessManager->userHasAccessToOnlyEditStatusForMentorshipSessions($accountManager));
        $this->assertFalse($this->accessManager->userIsAdmin($accountManager));
        $this->assertFalse($this->accessManager->userHasAccessToCRUDMentorshipSessions($accountManager));
    }

    public function test_a_revoked_role_stops_at_once() {
        $admin = $this->makeBackofficeUser($this->accessManager->ADMINISTRATOR_ROLE_ID, 'Admin');
        $this->assertTrue($this->accessManager->userIsAdmin($admin));

        DB::table('user_role')->where('user_id', $admin->id)->update(['deleted_at' => now()]);

        $this->assertFalse($this->accessManager->userIsAdmin($admin->fresh()));
    }
}
