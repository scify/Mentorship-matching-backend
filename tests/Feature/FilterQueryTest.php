<?php

use App\BusinessLogicLayer\managers\MenteeManager;
use App\BusinessLogicLayer\managers\MentorManager;
use App\BusinessLogicLayer\managers\MentorshipSessionManager;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Exercises the hand-written filter builders (the endpoints that used to be
 * SQL-injectable):
 *  - every filter combination builds a runnable query (placeholder/binding parity)
 *  - injection payloads are neutralised (bound, never parsed as SQL)
 *  - apostrophes in names still match (previously broke the concatenated query)
 *
 * Converted from tests/manual/verify_filters.php so `vendor/bin/phpunit` (and
 * CI) runs it; fixtures roll back with the test transaction.
 */
class FilterQueryTest extends TestCase
{
    use CreatesMatchingFixtures;
    use DatabaseTransactions;

    /** @var array<array{sql: string, bindings: array}> */
    private array $queryLog = [];

    private MentorManager $mentorManager;
    private MenteeManager $menteeManager;
    private MentorshipSessionManager $sessionManager;

    private int $companyId;
    private int $otherCompanyId;
    private int $mentorId;

    protected function setUp(): void
    {
        parent::setUp();
        // The app (and its DB connection) is rebuilt per test, so the listener
        // must be re-registered here rather than once per class.
        DB::listen(function ($q) {
            $this->queryLog[] = ['sql' => $q->sql, 'bindings' => $q->bindings];
        });

        $this->mentorManager = new MentorManager();
        $this->menteeManager = new MenteeManager();
        $this->sessionManager = new MentorshipSessionManager();

        $user = $this->makeBackofficeUser(3, 'Accman');
        $this->companyId = $this->makeCompany($user, 'Verify Co');
        $this->otherCompanyId = $this->makeCompany($user, 'Verify Co 2');
        // apostrophes in the seeded names are deliberate — they used to break
        // the string-concatenated query
        $this->mentorId = $this->makeMentorProfile($user, $this->companyId, [
            'first_name' => "O'Brien-Verify", 'last_name' => 'Verifytester',
        ]);
        // a second mentor at a DIFFERENT company makes injection outcomes
        // distinguishable from clean-filter outcomes
        $this->makeMentorProfile($user, $this->otherCompanyId, [
            'first_name' => 'Second', 'last_name' => 'Verifymentor',
        ]);
        $this->makeMenteeProfile($user, [
            'first_name' => "O'Brien-Verify", 'last_name' => 'Verifytester',
            'university_name' => "St Verify's John",
        ]);
    }

    /**
     * The managers run the hand-written filter query first, then a second
     * Eloquent query to hydrate the matched ids. Only the first one is what
     * we're auditing, so pick it out rather than inspecting whatever ran last.
     *
     * @return array{sql: string, bindings: array}
     */
    private function rawFilterQuery(): array
    {
        foreach (array_reverse($this->queryLog) as $q) {
            if (stripos($q['sql'], 'select distinct') === 0) {
                return $q;
            }
        }
        $this->fail('no raw filter query was captured');
    }

    public function testEveryMentorFilterCombinationBuildsARunnableQuery()
    {
        $specialtyId = (string)$this->lookupId('specialty');
        $residenceId = (string)$this->lookupId('residence');
        $filters = [
            ['mentorName' => 'Verifytester'],
            ['ageRange' => '20;60'],
            ['specialtyId' => $specialtyId],
            ['companyId' => (string)$this->companyId],
            ['availabilityId' => '1'],
            ['residenceId' => $residenceId],
            ['completedSessionsCount' => '2'],
            ['completedSessionsCount' => '7'],
            ['averageRating' => '4'],
            ['displayOnlyExternallySubscribed' => 'true'],
            ['displayOnlyAvailableWithCancelledSessions' => 'true'],
            // combinations that mix joins + where, where binding order matters most
            ['averageRating' => '4', 'mentorName' => 'Verifytester', 'companyId' => '1'],
            ['completedSessionsCount' => '3', 'ageRange' => '20;60', 'residenceId' => '1'],
            ['averageRating' => '5', 'completedSessionsCount' => '6', 'displayOnlyAvailableWithCancelledSessions' => 'true', 'mentorName' => 'x'],
        ];
        foreach ($filters as $filter) {
            $result = $this->mentorManager->getMentorViewModelsByCriteria($filter);
            $this->assertNotNull($result, 'mentor filter ' . json_encode($filter));
        }
    }

    public function testEveryMenteeFilterCombinationBuildsARunnableQuery()
    {
        $filters = [
            ['menteeName' => 'Verifytester'],
            ['educationLevel' => (string)$this->lookupId('education_level')],
            ['specialty' => (string)$this->lookupId('specialty')],
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
            ['averageRating' => '3', 'menteeName' => 'Verifytester', 'skills' => 'php,sql', 'displayOnlyActiveSession' => 'true'],
            ['completedSessionAgo' => '4', 'signedUpAgo' => '6', 'university' => 'Athens', 'ageRange' => '20;40'],
        ];
        foreach ($filters as $filter) {
            $result = $this->menteeManager->getMenteeViewModelsByCriteria($filter);
            $this->assertNotNull($result, 'mentee filter ' . json_encode($filter));
        }
    }

    public function testEverySessionFilterCombinationBuildsARunnableQuery()
    {
        $filters = [
            ['mentorName' => 'Verifytester'],
            ['menteeName' => 'Verifytester'],
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
        foreach ($filters as $filter) {
            $result = $this->sessionManager->getMentorshipSessionViewModelsByCriteria($filter);
            $this->assertNotNull($result, 'session filter ' . json_encode($filter));
        }
    }

    public function testNameFiltersMatchTheSeededProfilesIncludingApostrophes()
    {
        $this->assertCount(1, $this->mentorManager->getMentorViewModelsByCriteria(['mentorName' => 'Verifytester']));
        // apostrophe: would have produced a broken/injected query under the old string concat
        $this->assertCount(1, $this->mentorManager->getMentorViewModelsByCriteria(['mentorName' => "O'Brien-Verify"]));
        $this->assertCount(0, $this->mentorManager->getMentorViewModelsByCriteria(['mentorName' => 'NoSuchPersonXYZ']));
        $this->assertCount(1, $this->menteeManager->getMenteeViewModelsByCriteria(['university' => "St Verify's John"]));
    }

    public function testIntvalBypassPayloadDoesNotWidenTheResultSet()
    {
        // the classic intval() bypass: intval("1 or 1=1") === 1, so the old guard let it through.
        //   injected -> `company_id = 1 or 1=1` matches every mentor
        //   bound    -> `company_id = ?` cast to the clean id matches only the fixture mentor
        $total = count($this->mentorManager->getMentorViewModelsByCriteria([]));
        $baseline = count($this->mentorManager->getMentorViewModelsByCriteria(['companyId' => (string)$this->companyId]));

        $injected = count($this->mentorManager->getMentorViewModelsByCriteria(['companyId' => $this->companyId . ' or 1=1']));

        $this->assertSame($baseline, $injected, '`or 1=1` must have no effect on the result set');
        $this->assertLessThan($total, $injected, 'payload must not match every mentor');
        $this->assertStringNotContainsString('1=1', $this->rawFilterQuery()['sql'],
            'payload must never reach the SQL string');
    }

    public function testUnionPayloadInANumericFilterReturnsNothing()
    {
        try {
            $result = $this->mentorManager->getMentorViewModelsByCriteria(
                ['specialtyId' => '1 union select id from users']);
            $this->assertCount(0, $result, 'UNION payload must not exfiltrate rows');
        } catch (\Throwable $e) {
            // rejecting the payload outright is equally acceptable
            $this->addToAssertionCount(1);
        }
    }

    public function testQuoteBreakoutStaysInTheBindingsNeverInTheSql()
    {
        $result = $this->mentorManager->getMentorViewModelsByCriteria(
            ['mentorName' => "%' union select id,1,1,1,1,1 from users -- "]);

        $this->assertCount(0, $result);
        $raw = $this->rawFilterQuery();
        $this->assertStringNotContainsStringIgnoringCase('union', $raw['sql']);
        $payloadIsBound = false;
        foreach ($raw['bindings'] as $binding) {
            if (is_string($binding) && str_contains($binding, 'union select')) {
                $payloadIsBound = true;
            }
        }
        $this->assertTrue($payloadIsBound, 'payload must appear in the bindings, not the SQL');
    }

    public function testDropTablePayloadIsHarmless()
    {
        try {
            $this->menteeManager->getMenteeViewModelsByCriteria(['university' => "x'; drop table mentor_rating; -- "]);
        } catch (\Throwable $e) {
            // a rejection is fine — as long as nothing was dropped
        }
        $this->assertTrue(Schema::hasTable('mentor_rating'), 'TABLE WAS DROPPED');
    }

    public function testMalformedDateRangeIsRejected()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Filter value is not valid.');
        $this->sessionManager->getMentorshipSessionViewModelsByCriteria(
            ['startedDateRange' => "01/01/2020 - 2025') or 1=1 -- /12/31"]);
    }

    public function testSkillsPartsAreBoundSeparately()
    {
        // skills is exploded on comma — each part must be bound on its own
        $this->menteeManager->getMenteeViewModelsByCriteria(['skills' => "php,%' or '1'='1"]);

        $raw = $this->rawFilterQuery();
        $this->assertStringNotContainsString("or '1'='1", $raw['sql']);
        $this->assertContains("%%' or '1'='1%", $raw['bindings'],
            'payload must stay in the bindings, never in the SQL');
    }

    public function testGeneratedSqlContainsOnlyPlaceholdersNotValues()
    {
        $this->mentorManager->getMentorViewModelsByCriteria([
            'mentorName' => 'Verifytester', 'companyId' => (string)$this->companyId,
            'ageRange' => '20;60', 'averageRating' => '4',
        ]);

        $raw = $this->rawFilterQuery();
        $this->assertGreaterThan(0, count($raw['bindings']));
        $this->assertSame(substr_count($raw['sql'], '?'), count($raw['bindings']),
            'placeholder count must match binding count');
    }
}
