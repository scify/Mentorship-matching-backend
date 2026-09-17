<?php

use App\BusinessLogicLayer\managers\UserAccessManager;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Characterizes the role checks every route middleware delegates to.
 * The cache-staleness test captures documented current behavior (role
 * changes are invisible until the cache is cleared), not desired behavior.
 */
class UserAccessManagerTest extends TestCase
{
    use CreatesMatchingFixtures;
    use DatabaseTransactions;

    private UserAccessManager $accessManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accessManager = new UserAccessManager();
        Cache::flush(); // role checks are cached per user id — isolate each test
    }

    public function testAdminHasEveryAdminOnlyAccess()
    {
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

    public function testMatcherHasNoAdminOrAccountManagerAccess()
    {
        $matcher = $this->makeBackofficeUser($this->accessManager->MATCHER_ROLE_ID, 'Matcher');

        $this->assertTrue($this->accessManager->userIsMatcher($matcher));
        $this->assertFalse($this->accessManager->userIsAdmin($matcher));
        $this->assertFalse($this->accessManager->userHasAccessToCRUDSystemUsers($matcher));
        $this->assertFalse($this->accessManager->userHasAccessToEditMentorsAndMentees($matcher));
        $this->assertFalse($this->accessManager->userHasAccessToOnlyEditStatusForMentorshipSessions($matcher));
    }

    public function testAccountManagerCanEditProfilesAndSessionStatusButIsNotAdmin()
    {
        $accountManager = $this->makeBackofficeUser($this->accessManager->ACCOUNT_MANAGER_ROLE_ID, 'Accman');

        $this->assertTrue($this->accessManager->userIsAccountManager($accountManager));
        $this->assertTrue($this->accessManager->userHasAccessToEditMentorsAndMentees($accountManager));
        $this->assertTrue($this->accessManager->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees($accountManager));
        $this->assertTrue($this->accessManager->userHasAccessToOnlyEditStatusForMentorshipSessions($accountManager));
        $this->assertFalse($this->accessManager->userIsAdmin($accountManager));
        $this->assertFalse($this->accessManager->userHasAccessToCRUDMentorshipSessions($accountManager));
    }

    public function testRevokedRoleStaysGrantedUntilTheCacheIsCleared()
    {
        // This captures current behavior, not necessarily correct behavior:
        // checkCacheOrDBForRoleAndStore caches positive checks with no TTL or
        // invalidation, so a revoked admin keeps admin access until the cache
        // is flushed (see CLAUDE.md, "Authorization model").
        $admin = $this->makeBackofficeUser($this->accessManager->ADMINISTRATOR_ROLE_ID, 'Admin');
        $this->assertTrue($this->accessManager->userIsAdmin($admin));

        DB::table('user_role')->where('user_id', $admin->id)->update(['deleted_at' => now()]);

        $this->assertTrue($this->accessManager->userIsAdmin($admin->fresh()),
            'revoked role is still served from cache — if this fails, cache invalidation was added; update CLAUDE.md');

        Cache::flush();
        $this->assertFalse($this->accessManager->userIsAdmin($admin->fresh()),
            'after a cache flush the revocation must be visible');
    }
}
