<?php
/**
 * Exercises the rewritten filter builders against the live DB.
 *  - asserts every filter combination executes (placeholder/binding counts match)
 *  - asserts SQL injection payloads are neutralised (bound, not parsed as SQL)
 *  - asserts apostrophes in names still work (previously would have broken the query)
 *
 * Run: docker exec mentorship_matching_platform_server php /var/www/tests/manual/verify_filters.php
 */

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\BusinessLogicLayer\managers\MenteeManager;
use App\BusinessLogicLayer\managers\MentorManager;
use App\BusinessLogicLayer\managers\MentorshipSessionManager;
use Illuminate\Support\Facades\DB;

$pass = 0;
$fail = 0;
$queryLog = [];

DB::listen(function ($q) use (&$queryLog) {
    $queryLog[] = ['sql' => $q->sql, 'bindings' => $q->bindings];
});

/**
 * The managers run the hand-written filter query first, then a second Eloquent
 * query to hydrate the matched ids. Only the first one is what we're auditing,
 * so pick it out rather than inspecting whatever ran last.
 */
function rawFilterQuery(): array {
    global $queryLog;
    foreach (array_reverse($queryLog) as $q) {
        if (stripos($q['sql'], 'select distinct') === 0) {
            return $q;
        }
    }
    return ['sql' => '<no raw filter query captured>', 'bindings' => []];
}
function resetLog() { global $queryLog; $queryLog = []; }

function ok($msg) { global $pass; $pass++; echo "  PASS  $msg\n"; }
function bad($msg) { global $fail; $fail++; echo "  FAIL  $msg\n"; }

// ---------------------------------------------------------------- seed fixtures
DB::table('mentor_profile')->where('email', 'like', 'verify-%')->delete();
DB::table('mentee_profile')->where('email', 'like', 'verify-%')->delete();

// pick real lookup ids so the FKs hold
$userId      = DB::table('users')->value('id');
$referenceId = DB::table('reference')->value('id');
$residenceId = DB::table('residence')->value('id');
$universityId = DB::table('university')->value('id');
$educationId = DB::table('education_level')->value('id');
$specialtyId = DB::table('specialty')->value('id');
$companyId   = DB::table('company')->value('id');
if (!$companyId) {
    $companyId = DB::table('company')->insertGetId([
        'name' => 'Verify Co', 'account_manager_id' => $userId,
        'created_at' => now(), 'updated_at' => now(),
    ]);
}

$mentorId = DB::table('mentor_profile')->insertGetId([
    'creator_user_id' => $userId, 'reference_id' => $referenceId, 'first_name' => "O'Brien", 'last_name' => 'Tester',
    'year_of_birth' => 1985, 'residence_id' => $residenceId, 'email' => 'verify-mentor@example.test',
    'education_level_id' => $educationId, 'university_id' => $universityId, 'company_id' => $companyId, 'status_id' => 1,
    'created_at' => now(), 'updated_at' => now(),
]);
$menteeId = DB::table('mentee_profile')->insertGetId([
    'creator_user_id' => $userId, 'reference_id' => $referenceId, 'first_name' => "O'Brien", 'last_name' => 'Tester',
    'year_of_birth' => 1998, 'residence_id' => $residenceId, 'email' => 'verify-mentee@example.test',
    'education_level_id' => $educationId, 'university_id' => $universityId, 'university_name' => "St John's",
    'skills' => 'php, sql', 'is_employed' => 0, 'status_id' => 1,
    'created_at' => now(), 'updated_at' => now(),
]);
echo "Seeded mentor #$mentorId / mentee #$menteeId (company #$companyId)\n\n";

$mentorManager = new MentorManager();
$menteeManager = new MenteeManager();
$sessionManager = new MentorshipSessionManager();

// ---------------------------------------------------------------- 1. every filter executes
echo "1. Each filter builds a runnable query\n";

$mentorFilters = [
    ['mentorName' => 'Tester'],
    ['ageRange' => '20;60'],
    ['specialtyId' => (string)$specialtyId],
    ['companyId' => (string)$companyId],
    ['availabilityId' => '1'],
    ['residenceId' => (string)$residenceId],
    ['completedSessionsCount' => '2'],
    ['completedSessionsCount' => '7'],
    ['averageRating' => '4'],
    ['displayOnlyExternallySubscribed' => 'true'],
    ['displayOnlyAvailableWithCancelledSessions' => 'true'],
    // combinations that mix joins + where, where binding order matters most
    ['averageRating' => '4', 'mentorName' => 'Tester', 'companyId' => '1'],
    ['completedSessionsCount' => '3', 'ageRange' => '20;60', 'residenceId' => '1'],
    ['averageRating' => '5', 'completedSessionsCount' => '6', 'displayOnlyAvailableWithCancelledSessions' => 'true', 'mentorName' => 'x'],
];
foreach ($mentorFilters as $f) {
    try {
        $mentorManager->getMentorViewModelsByCriteria($f);
        ok('mentor ' . json_encode($f));
    } catch (\Throwable $e) {
        bad('mentor ' . json_encode($f) . ' -> ' . $e->getMessage());
    }
}

$menteeFilters = [
    ['menteeName' => 'Tester'],
    ['educationLevel' => (string)$educationId],
    ['specialty' => (string)$specialtyId],
    ['university' => '1'],
    ['university' => 'Athens University'],
    ['displayOnlyActiveSession' => 'true'],
    ['ageRange' => '20;40'],
    ['skills' => 'php,sql'],
    ['signedUpAgo' => '3'],
    ['signedUpAgo' => '24'],
    ['completedSessionAgo' => '5'],
    ['averageRating' => '3'],
    ['displayOnlyUnemployed' => 'true'],
    ['displayOnlyNeverMatched' => 'true'],
    ['displayOnlyExternallySubscribed' => 'true'],
    ['displayOnlyAvailableWithCancelledSessions' => 'true'],
    ['displayOnlyAvailable' => 'true'],
    ['averageRating' => '3', 'menteeName' => 'Tester', 'skills' => 'php,sql', 'displayOnlyActiveSession' => 'true'],
    ['completedSessionAgo' => '4', 'signedUpAgo' => '6', 'university' => 'Athens', 'ageRange' => '20;40'],
];
foreach ($menteeFilters as $f) {
    try {
        $menteeManager->getMenteeViewModelsByCriteria($f);
        ok('mentee ' . json_encode($f));
    } catch (\Throwable $e) {
        bad('mentee ' . json_encode($f) . ' -> ' . $e->getMessage());
    }
}

$sessionFilters = [
    ['mentorName' => 'Tester'],
    ['menteeName' => 'Tester'],
    ['startStatusId' => '2'],
    ['endStatusId' => '9'],
    ['startedDateRange' => '01/01/2020 - 31/12/2025'],
    ['completedDateRange' => '01/01/2020 - 31/12/2025'],
    ['accountManagerId' => '1'],
    ['matcherId' => '1'],
    ['userRole' => 'account_manager'],
    ['userRole' => 'matcher'],
    ['completedDateRange' => '01/01/2020 - 31/12/2025', 'mentorName' => 'a', 'menteeName' => 'b',
     'startStatusId' => '1', 'endStatusId' => '9', 'startedDateRange' => '01/01/2021 - 31/12/2024',
     'accountManagerId' => '1', 'matcherId' => '1', 'userRole' => 'matcher'],
];
foreach ($sessionFilters as $f) {
    try {
        $sessionManager->getMentorshipSessionViewModelsByCriteria($f);
        ok('session ' . json_encode($f));
    } catch (\Throwable $e) {
        bad('session ' . json_encode($f) . ' -> ' . $e->getMessage());
    }
}

// ---------------------------------------------------------------- 2. results still correct
echo "\n2. Filters still return the right rows\n";
$r = $mentorManager->getMentorViewModelsByCriteria(['mentorName' => 'Tester']);
count($r) === 1 ? ok("mentorName='Tester' matched the seeded mentor") : bad("expected 1 mentor, got " . count($r));

// apostrophe: would have produced a broken/injected query under the old string concat
$r = $mentorManager->getMentorViewModelsByCriteria(['mentorName' => "O'Brien"]);
count($r) === 1 ? ok("mentorName=\"O'Brien\" matched (apostrophe handled)") : bad("expected 1 mentor for O'Brien, got " . count($r));

$r = $mentorManager->getMentorViewModelsByCriteria(['mentorName' => 'NoSuchPersonXYZ']);
count($r) === 0 ? ok('non-matching name returns nothing') : bad('expected 0, got ' . count($r));

$r = $menteeManager->getMenteeViewModelsByCriteria(['university' => "St John's"]);
count($r) === 1 ? ok("university=\"St John's\" matched (apostrophe handled)") : bad("expected 1 mentee, got " . count($r));

// ---------------------------------------------------------------- 3. injection payloads
echo "\n3. Injection payloads are neutralised\n";

// 3a. the classic intval() bypass: intval("1 or 1=1") === 1, so the old guard let it through.
// A second mentor at a DIFFERENT company makes the two outcomes distinguishable:
//   injected -> `company_id = 1 or 1=1` matches every mentor
//   bound    -> `company_id = ?` with '1 or 1=1' cast to 1 matches only the first
$otherCompanyId = DB::table('company')->insertGetId([
    'name' => 'Verify Co 2', 'account_manager_id' => $userId,
    'created_at' => now(), 'updated_at' => now(),
]);
DB::table('mentor_profile')->insert([
    'creator_user_id' => $userId, 'reference_id' => $referenceId, 'first_name' => 'Second', 'last_name' => 'Mentor',
    'year_of_birth' => 1990, 'residence_id' => $residenceId, 'email' => 'verify-mentor2@example.test',
    'education_level_id' => $educationId, 'university_id' => $universityId, 'company_id' => $otherCompanyId,
    'status_id' => 1, 'created_at' => now(), 'updated_at' => now(),
]);
$total = count($mentorManager->getMentorViewModelsByCriteria([]));
echo "  (mentors in table: $total, in company #$companyId: 1)\n";
try {
    resetLog();
    $baseline = count($mentorManager->getMentorViewModelsByCriteria(['companyId' => (string)$companyId]));
    $inj = count($mentorManager->getMentorViewModelsByCriteria(['companyId' => $companyId . ' or 1=1']));
    if ($inj === $baseline && $inj < $total) {
        ok("companyId='$companyId or 1=1' matched $inj row(s) like the clean filter, not all $total — `or 1=1` had no effect");
    } else {
        bad("companyId injection matched $inj rows (clean filter: $baseline, table total: $total)");
    }
    $raw = rawFilterQuery();
    if (strpos($raw['sql'], '1=1') === false) {
        ok('companyId payload never reached the SQL string');
    } else {
        bad('companyId payload was interpolated into SQL: ' . $raw['sql']);
    }
} catch (\Throwable $e) {
    ok("companyId='1 or 1=1' rejected: " . $e->getMessage());
}

resetLog();
// 3b. UNION exfiltration of the users table via a numeric filter
try {
    $inj = $mentorManager->getMentorViewModelsByCriteria(
        ['specialtyId' => '1 union select id from users']
    );
    count($inj) === 0
        ? ok("specialtyId UNION payload returned 0 rows")
        : bad("specialtyId UNION payload returned " . count($inj) . " rows");
} catch (\Throwable $e) {
    ok("specialtyId UNION payload rejected: " . $e->getMessage());
}

resetLog();
// 3c. quote breakout through the free-text name filter
try {
    $inj = $mentorManager->getMentorViewModelsByCriteria(
        ['mentorName' => "%' union select id,1,1,1,1,1 from users -- "]
    );
    count($inj) === 0
        ? ok('mentorName quote-breakout returned 0 rows')
        : bad('mentorName quote-breakout returned ' . count($inj) . ' rows');
    $raw = rawFilterQuery();
    $payloadIsBound = false;
    foreach ($raw['bindings'] as $b) {
        if (is_string($b) && strpos($b, 'union select') !== false) {
            $payloadIsBound = true;
        }
    }
    if (stripos($raw['sql'], 'union') === false && $payloadIsBound) {
        ok('mentorName payload stayed in the bindings, never in the SQL');
    } else {
        bad('mentorName payload reached SQL: ' . $raw['sql']);
    }
} catch (\Throwable $e) {
    bad('mentorName payload raised: ' . $e->getMessage());
}

// 3d. destructive payload — must not drop anything
try {
    $menteeManager->getMenteeViewModelsByCriteria(['university' => "x'; drop table mentor_rating; -- "]);
    ok('drop-table payload executed harmlessly');
} catch (\Throwable $e) {
    ok('drop-table payload rejected: ' . $e->getMessage());
}
Illuminate\Support\Facades\Schema::hasTable('mentor_rating')
    ? ok('mentor_rating table still exists')
    : bad('TABLE WAS DROPPED');

// 3e. date range breakout
try {
    $sessionManager->getMentorshipSessionViewModelsByCriteria(
        ['startedDateRange' => "01/01/2020 - 2025') or 1=1 -- /12/31"]
    );
    bad('malformed date range was accepted');
} catch (\Throwable $e) {
    ok('malformed date range rejected: ' . $e->getMessage());
}

resetLog();
// 3f. skills is exploded on comma - each part must be bound separately
try {
    $menteeManager->getMenteeViewModelsByCriteria(['skills' => "php,%' or '1'='1"]);
    $raw = rawFilterQuery();
    strpos($raw['sql'], "or '1'='1") === false && in_array("%%' or '1'='1%", $raw['bindings'], true)
        ? ok('skills payload stayed in the bindings, never in the SQL')
        : bad('skills payload reached SQL: ' . $raw['sql']);
} catch (\Throwable $e) {
    bad('skills payload raised: ' . $e->getMessage());
}

// ---------------------------------------------------------------- 4. no user input in SQL text
echo "\n4. Generated SQL contains placeholders, not values\n";
resetLog();
$mentorManager->getMentorViewModelsByCriteria(
    ['mentorName' => 'Tester', 'companyId' => (string)$companyId, 'ageRange' => '20;60', 'averageRating' => '4']
);
$raw = rawFilterQuery();
echo "  SQL: " . preg_replace('/\s+/', ' ', $raw['sql']) . "\n";
echo "  Bindings: " . json_encode($raw['bindings']) . "\n";
substr_count($raw['sql'], '?') === count($raw['bindings']) && count($raw['bindings']) > 0
    ? ok('placeholder count matches binding count (' . count($raw['bindings']) . ')')
    : bad('placeholder/binding mismatch: ' . substr_count($raw['sql'], '?') . ' vs ' . count($raw['bindings']));

// ---------------------------------------------------------------- cleanup
DB::table('mentor_profile')->where('email', 'like', 'verify-%')->delete();
DB::table('mentee_profile')->where('email', 'like', 'verify-%')->delete();
DB::table('company')->where('name', 'like', 'Verify Co%')->delete();

echo "\n==================================\n";
echo "PASS: $pass   FAIL: $fail\n";
exit($fail > 0 ? 1 : 0);
