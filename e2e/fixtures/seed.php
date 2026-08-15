<?php
/**
 * Seeds the minimum reference data the end-to-end suite needs on top of
 * `php artisan migrate:fresh --seed`:
 *   - a company (the public mentor form requires a non-empty company list)
 *   - one backoffice user per role, all with the password below
 *
 * Run: docker exec mentorship_matching_platform_server php /var/www/e2e/fixtures/seed.php
 */

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\eloquent\AccountManagerCapacity;
use App\Models\eloquent\Company;
use App\Models\eloquent\User;
use App\Models\eloquent\UserRole;
use Illuminate\Support\Facades\DB;

const PASSWORD = 'password123';

/*
 * Remove what previous runs registered (emails are `e2e.<kind>.<runid>@example.test`)
 * so repeated runs don't accumulate rows or skew count-based assertions.
 * Children first: the profile tables are referenced by pivots, history and sessions.
 */
$staleMentorIds = DB::table('mentor_profile')->where('email', 'like', 'e2e.%@example.test')->pluck('id');
$staleMenteeIds = DB::table('mentee_profile')->where('email', 'like', 'e2e.%@example.test')->pluck('id');
$staleSessionIds = DB::table('mentorship_session')
    ->whereIn('mentor_profile_id', $staleMentorIds)
    ->orWhereIn('mentee_profile_id', $staleMenteeIds)
    ->pluck('id');
DB::table('mentorship_session_history')->whereIn('mentorship_session_id', $staleSessionIds)->delete();
DB::table('mentorship_session')->whereIn('id', $staleSessionIds)->delete();
foreach (['mentor_specialty', 'mentor_industry', 'mentor_status_history'] as $table) {
    DB::table($table)->whereIn('mentor_profile_id', $staleMentorIds)->delete();
}
foreach (['mentee_specialty', 'mentee_status_history'] as $table) {
    DB::table($table)->whereIn('mentee_profile_id', $staleMenteeIds)->delete();
}
DB::table('mentor_profile')->whereIn('id', $staleMentorIds)->delete();
DB::table('mentee_profile')->whereIn('id', $staleMenteeIds)->delete();

$staleUserIds = DB::table('users')->where('email', 'like', 'e2e.%@example.test')->pluck('id');
DB::table('user_role')->whereIn('user_id', $staleUserIds)->delete();
DB::table('users')->whereIn('id', $staleUserIds)->delete();

const ROLE_ADMINISTRATOR = 1;
const ROLE_MATCHER = 2;
const ROLE_ACCOUNT_MANAGER = 3;

function makeUser(string $email, string $first, string $last, int $roleId): User {
    $user = User::withTrashed()->where('email', $email)->first();
    if (!$user) {
        $user = new User();
        $user->email = $email;
    }
    $user->first_name = $first;
    $user->last_name = $last;
    $user->password = bcrypt(PASSWORD);
    $user->state_id = 1;          // active - the login flow filters on this
    $user->user_icon_id = 1;
    $user->deleted_at = null;
    $user->save();

    UserRole::firstOrCreate(['user_id' => $user->id, 'role_id' => $roleId]);
    return $user;
}

$admin      = makeUser('admin@jobpairs.test', 'Ada', 'Admin', ROLE_ADMINISTRATOR);
$matcher    = makeUser('matcher@jobpairs.test', 'Mat', 'Matcher', ROLE_MATCHER);
$accManager = makeUser('accman@jobpairs.test', 'Alex', 'Manager', ROLE_ACCOUNT_MANAGER);

// account managers need capacity or they cannot be assigned to a session
AccountManagerCapacity::updateOrCreate(
    ['account_manager_id' => $accManager->id],
    ['capacity' => 10]
);

$company = Company::firstOrCreate(
    ['name' => 'Acme Corp'],
    [
        'description' => 'E2E fixture company',
        'website' => 'https://acme.test',
        'hr_contact_details' => 'hr@acme.test',
        'account_manager_id' => $accManager->id,
    ]
);

echo json_encode([
    'admin_id' => $admin->id,
    'matcher_id' => $matcher->id,
    'account_manager_id' => $accManager->id,
    'company_id' => $company->id,
    'specialty_id' => DB::table('specialty')->value('id'),
    'industry_id' => DB::table('industry')->value('id'),
    'reference_id' => DB::table('reference')->value('id'),
], JSON_PRETTY_PRINT) . PHP_EOL;
