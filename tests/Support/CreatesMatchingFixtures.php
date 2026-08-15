<?php

use App\Models\eloquent\MentorshipSession;
use App\Models\eloquent\User;
use App\Models\eloquent\UserRole;
use Illuminate\Support\Facades\DB;

/**
 * Builds the users, profiles and sessions the feature tests exercise.
 *
 * Every row is created inside the test's transaction (DatabaseTransactions),
 * so nothing leaks into the database between tests or runs. Lookup tables
 * (reference, residence, ...) are the app's seeded reference data and are a
 * hard prerequisite — the trait fails fast with a pointer to the seeder
 * instead of silently picking wrong rows.
 */
trait CreatesMatchingFixtures
{
    protected function lookupId(string $table): int {
        $id = DB::table($table)->value('id');
        if (!$id) {
            $this->fail("Lookup table '$table' is empty — run `php artisan migrate` and `php artisan db:seed` before the test suite.");
        }
        return (int)$id;
    }

    protected function makeBackofficeUser(int $roleId, string $label): User {
        $user = new User();
        $user->email = 'phpunit-' . strtolower($label) . '-' . uniqid() . '@example.test';
        $user->first_name = $label;
        $user->last_name = 'Fixture';
        $user->password = bcrypt('phpunit-password');
        $user->state_id = 1; // active — the login flow filters on this
        $user->user_icon_id = 1;
        $user->save();
        UserRole::create(['user_id' => $user->id, 'role_id' => $roleId]);
        return $user;
    }

    protected function makeCompany(User $accountManager, string $name): int {
        return (int)DB::table('company')->insertGetId([
            'name' => $name . ' ' . uniqid(),
            'account_manager_id' => $accountManager->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** @param array<string, mixed> $overrides */
    protected function makeMentorProfile(User $creator, int $companyId, array $overrides = []): int {
        return (int)DB::table('mentor_profile')->insertGetId(array_merge([
            'creator_user_id' => $creator->id,
            'reference_id' => $this->lookupId('reference'),
            'first_name' => 'Marina',
            'last_name' => 'Verifymentor',
            'year_of_birth' => 1985,
            'residence_id' => $this->lookupId('residence'),
            'email' => 'phpunit-mentor-' . uniqid() . '@example.test',
            'education_level_id' => $this->lookupId('education_level'),
            'university_id' => $this->lookupId('university'),
            'company_id' => $companyId,
            'status_id' => 1, // available for mentorship
            'created_at' => now(), 'updated_at' => now(),
        ], $overrides));
    }

    /** @param array<string, mixed> $overrides */
    protected function makeMenteeProfile(User $creator, array $overrides = []): int {
        return (int)DB::table('mentee_profile')->insertGetId(array_merge([
            'creator_user_id' => $creator->id,
            'reference_id' => $this->lookupId('reference'),
            'first_name' => 'Nikos',
            'last_name' => 'Verifymentee',
            'year_of_birth' => 1998,
            'residence_id' => $this->lookupId('residence'),
            'email' => 'phpunit-mentee-' . uniqid() . '@example.test',
            'education_level_id' => $this->lookupId('education_level'),
            'university_id' => $this->lookupId('university'),
            'university_name' => 'Verify University',
            'skills' => 'php, sql',
            'is_employed' => 0,
            'status_id' => 1, // available
            'created_at' => now(), 'updated_at' => now(),
        ], $overrides));
    }

    /** A session already at $statusId, with mentor/mentee marked matched (2/2), as the app leaves them. */
    protected function makeSessionAtStatus(int $mentorProfileId, int $menteeProfileId,
                                           User $accountManager, User $matcher, int $statusId): MentorshipSession {
        $session = MentorshipSession::create([
            'mentor_profile_id' => $mentorProfileId,
            'mentee_profile_id' => $menteeProfileId,
            'account_manager_id' => $accountManager->id,
            'matcher_id' => $matcher->id,
            'status_id' => $statusId,
            'general_comment' => '',
        ]);
        DB::table('mentor_profile')->where('id', $mentorProfileId)->update(['status_id' => 2]);
        DB::table('mentee_profile')->where('id', $menteeProfileId)->update(['status_id' => 2]);
        return $session;
    }

    protected function mentorStatusId(int $mentorProfileId): int {
        return (int)DB::table('mentor_profile')->where('id', $mentorProfileId)->value('status_id');
    }

    protected function menteeStatusId(int $menteeProfileId): int {
        return (int)DB::table('mentee_profile')->where('id', $menteeProfileId)->value('status_id');
    }

    /** @return array<int> the session's history status ids, oldest first */
    protected function sessionHistoryStatusIds(int $sessionId): array {
        return DB::table('mentorship_session_history')
            ->where('mentorship_session_id', $sessionId)
            ->orderBy('id')->pluck('status_id')->map(fn ($id) => (int)$id)->all();
    }
}
