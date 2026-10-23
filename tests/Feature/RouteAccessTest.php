<?php

use App\Models\eloquent\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route as Router;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Who may reach which route. The routes come from the router, so a new route
 * in a protected group is covered without a change here.
 */
class RouteAccessTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    private const DENIED = 'You have no access to this page';

    private const ADMIN = 1;

    private const MATCHER = 2;

    private const ACCOUNT_MANAGER = 3;

    /**
     * Middleware group => role ids that pass it, and one route that the
     * allowed roles open (method, uri; {mentor} becomes a fixture mentor).
     */
    public static function roleGroups(): array {
        return [
            'admin' => ['admin', [self::ADMIN], ['GET', 'users/all']],
            'status-changer' => ['status-changer', [self::ACCOUNT_MANAGER], ['POST', 'mentor/changeStatus']],
            'can-edit-mentors-and-mentees' => ['can-edit-mentors-and-mentees', [self::ADMIN, self::ACCOUNT_MANAGER], ['GET', 'mentor/{mentor}/edit']],
            'can-create-mentorship-session' => ['can-create-mentorship-session', [self::ADMIN, self::MATCHER], ['GET', 'sessions/myMatches']],
            'can-update-sessions' => ['can-update-sessions', [self::ADMIN, self::MATCHER, self::ACCOUNT_MANAGER], ['GET', 'sessions/all']],
            'account-manager' => ['account-manager', [self::ACCOUNT_MANAGER], ['GET', 'sessions/mySessions']],
            'can-invite-mentee' => ['can-invite-mentee', [self::ADMIN, self::ACCOUNT_MANAGER], ['POST', 'session/sendInviteToMentee']],
        ];
    }

    public static function publicPages(): array {
        return [
            'login' => ['login'],
            'password reset' => ['password/reset'],
            'public mentor form' => ['mentor/create'],
            'public mentee form' => ['mentee/create'],
        ];
    }

    /**
     * Every route without auth. Adding a route here makes it public: review it
     * first. The debugbar routes come from a dev package and are left out.
     */
    public function test_only_the_listed_routes_are_public() {
        $public = [];
        foreach (Router::getRoutes()->getRoutes() as $route) {
            $protected = array_filter($route->gatherMiddleware(), fn ($m) => is_string($m) && ($m === 'auth' || str_starts_with($m, 'auth:')));
            if (!$protected && !str_starts_with($route->uri(), '_debugbar')) {
                $public[] = implode('|', array_diff($route->methods(), ['HEAD'])) . ' ' . $route->uri();
            }
        }
        sort($public);

        $this->assertSame([
            'GET accept-session-management/{mentorshipSessionId}/{id}/{email}',
            'GET accept-session/{mentorshipSessionId}/{role}/{id}/{email}',
            'GET decline-session-confirmation/{mentorshipSessionId}/{role}/{id}/{email}',
            'GET decline-session-management/{mentorshipSessionId}/{id}/{email}',
            'GET decline-session/{mentorshipSessionId}/{role}/{id}/{email}',
            'GET login',
            'GET mentee/create',
            'GET mentor/create',
            'GET password/reset',
            'GET password/reset/{token}',
            'GET rateMentee/{sessionId}/{mentorId}/{menteeId}',
            'GET rateMentor/{sessionId}/{menteeId}/{mentorId}',
            'GET set-status-available/{id}/{email}',
            'POST login',
            'POST logout',
            'POST mentee/create',
            'POST mentor/create',
            'POST password/email',
            'POST password/reset',
            'POST rateMentee',
            'POST rateMentor',
        ], $public);
    }

    public function test_registration_the_api_and_the_test_routes_are_gone() {
        $admin = $this->makeBackofficeUser(self::ADMIN, 'Admin');
        $this->get('/register')->assertNotFound();
        $this->post('/register', ['email' => 'x@example.test'])->assertNotFound();
        $this->get('/api/user')->assertNotFound();
        $this->actingAs($admin)->get('/testemail?email=' . $admin->email)->assertNotFound();
        $this->actingAs($admin)->get('/testexception')->assertNotFound();
    }

    #[DataProvider('publicPages')]
    public function test_a_public_page_opens_for_a_guest(string $uri) {
        $this->get('/' . $uri)->assertOk();
    }

    public function test_a_guest_is_sent_to_the_login_page_from_every_protected_route() {
        $routes = $this->routesWithMiddleware('auth');
        $this->assertGreaterThan(40, count($routes));

        foreach ($routes as $route) {
            [$method, $uri] = $this->request($route, 1);
            $this->call($method, $uri)->assertRedirect('/login');
        }
    }

    public function test_every_protected_route_also_requires_an_active_staff_user() {
        foreach ($this->routesWithMiddleware('auth') as $route) {
            $this->assertContains('staff', $route->gatherMiddleware(), $route->uri());
        }
    }

    public function test_a_user_without_a_role_is_logged_out_from_every_protected_route() {
        $this->assertLoggedOutEverywhere($this->makeUserWithoutRole());
    }

    public function test_an_ajax_request_of_a_deactivated_user_gets_401() {
        $admin = $this->makeBackofficeUser(self::ADMIN, 'Deactivated');
        $admin->state_id = 2;
        $admin->save();

        $this->actingAs($admin)->getJson('/session/fetchHistory?mentorship_session_id=1')->assertStatus(401);
        $this->assertGuest();
    }

    public function test_a_deactivated_user_is_logged_out_from_every_protected_route() {
        $admin = $this->makeBackofficeUser(self::ADMIN, 'Deactivated');
        $admin->state_id = 2;
        $admin->save();

        $this->assertLoggedOutEverywhere($admin);
    }

    #[DataProvider('roleGroups')]
    public function test_every_route_of_a_role_group_denies_the_other_roles(string $group, array $allowed, array $openedRoute) {
        $routes = $this->routesWithMiddleware($group);
        $this->assertNotEmpty($routes, "No route uses the middleware group {$group}");

        foreach (array_diff([self::ADMIN, self::MATCHER, self::ACCOUNT_MANAGER], $allowed) as $roleId) {
            $this->actingAs($this->makeBackofficeUser($roleId, 'Role' . $roleId));
            foreach ($routes as $route) {
                [$method, $uri] = $this->request($route, 1);
                $this->assertDenied($this->call($method, $uri), "role {$roleId}, {$method} {$uri}");
            }
        }
    }

    #[DataProvider('roleGroups')]
    public function test_the_allowed_roles_pass_a_role_group(string $group, array $allowed, array $route) {
        [$method, $uri] = $route;
        if (str_contains($uri, '{mentor}')) {
            $creator = $this->makeBackofficeUser(self::ADMIN, 'Creator');
            $uri = str_replace('{mentor}', (string) $this->makeMentorProfile($creator, $this->makeCompany($creator, 'Company')), $uri);
        }
        $this->assertContains($group, Router::getRoutes()->match(request()->create('/' . $uri, $method))->gatherMiddleware());

        foreach ($allowed as $roleId) {
            $this->actingAs($this->makeBackofficeUser($roleId, 'Role' . $roleId));
            $this->flushSession();
            $response = $this->call($method, '/' . $uri);
            $this->assertNotContains(self::DENIED, $this->errorMessages(), "role {$roleId}, {$method} /{$uri}");
            $this->assertLessThan(500, $response->getStatusCode(), "role {$roleId}, {$method} /{$uri}");
        }
    }

    public function test_a_denial_does_not_follow_a_foreign_referer() {
        $this->actingAs($this->makeBackofficeUser(self::MATCHER, 'Matcher'));

        $response = $this->get('/users/all', ['Referer' => 'https://evil.example/phish']);

        $response->assertRedirect();
        $this->assertStringStartsWith(config('app.url'), $response->headers->get('Location'));
        $this->assertContains(self::DENIED, $this->errorMessages());
    }

    public function test_a_user_may_edit_only_their_own_profile_unless_admin() {
        $matcher = $this->makeBackofficeUser(self::MATCHER, 'Matcher');
        $accountManager = $this->makeBackofficeUser(self::ACCOUNT_MANAGER, 'AccMan');
        $admin = $this->makeBackofficeUser(self::ADMIN, 'Admin');
        $routes = $this->routesWithMiddleware('can-edit-user-profile');
        $this->assertNotEmpty($routes);

        foreach ([$matcher, $accountManager] as $user) {
            $this->actingAs($user);
            foreach ($routes as $route) {
                [$method, $uri] = $this->request($route, $admin->id);
                $this->assertDenied($this->call($method, $uri), "user {$user->id}, {$method} {$uri}");
            }
            $this->flushSession();
            $this->get("/user/{$user->id}/edit")->assertOk();
        }

        $this->actingAs($admin);
        $this->get("/user/{$matcher->id}/edit")->assertOk();
    }

    private function makeUserWithoutRole(): User {
        $user = $this->makeBackofficeUser(self::MATCHER, 'NoRole');
        DB::table('user_role')->where('user_id', $user->id)->delete();

        return $user->fresh();
    }

    private function assertLoggedOutEverywhere(User $user): void {
        $routes = $this->routesWithMiddleware('auth');
        foreach ($routes as $route) {
            $this->actingAs($user);
            [$method, $uri] = $this->request($route, $user->id);
            $this->call($method, $uri)->assertRedirect('/login');
            $this->assertGuest();
        }
    }

    /** @return list<Route> */
    private function routesWithMiddleware(string $middleware): array {
        return array_values(array_filter(
            Router::getRoutes()->getRoutes(),
            fn (Route $route) => in_array($middleware, $route->gatherMiddleware(), true),
        ));
    }

    /** @return array{0: string, 1: string} the first non-HEAD method and the uri with every parameter set to $id */
    private function request(Route $route, int $id): array {
        $method = array_values(array_diff($route->methods(), ['HEAD']))[0];

        return [$method, '/' . preg_replace('/\{\w+\??\}/', (string) $id, $route->uri())];
    }

    private function assertDenied($response, string $case): void {
        $response->assertRedirect();
        $this->assertContains(self::DENIED, $this->errorMessages(), $case);
        $this->flushSession();
    }

    /** @return list<string> */
    private function errorMessages(): array {
        $errors = session('errors');

        return $errors ? $errors->all() : [];
    }
}
