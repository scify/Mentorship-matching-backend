<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * The public mentor and mentee forms are open to anyone. A guest always gets
 * the public validation, and the server, not the request, decides the status,
 * the creator and the stored CV file name.
 */
class PublicProfileFormsTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    private const FORGED = ['status_id' => 3, 'creator_user_id' => 1, 'cv_file_name' => '../../other.pdf'];

    protected function setUp(): void {
        parent::setUp();
        Notification::fake();
    }

    public function test_a_guest_mentor_cannot_set_internal_fields() {
        $this->post('/mentor/create', $this->mentor() + self::FORGED)->assertSessionHasNoErrors();

        $row = DB::table('mentor_profile')->where('email', 'guest.mentor@example.test')->first();
        $this->assertNotNull($row);
        $this->assertSame(1, (int) $row->status_id);
        $this->assertNull($row->creator_user_id);
        $this->assertNull($row->cv_file_name);
    }

    public function test_a_guest_mentee_cannot_set_internal_fields() {
        $this->post('/mentee/create', $this->mentee() + self::FORGED)->assertSessionHasNoErrors();

        $row = DB::table('mentee_profile')->where('email', 'guest.mentee@example.test')->first();
        $this->assertNotNull($row);
        $this->assertSame(1, (int) $row->status_id);
        $this->assertNull($row->creator_user_id);
        $this->assertNull($row->cv_file_name);
    }

    public function test_a_guest_cannot_skip_the_public_validation() {
        $minimal = ['first_name' => 'A', 'last_name' => 'B', 'year_of_birth' => '1990', 'public_form' => 'false'];

        $this->post('/mentor/create', $minimal + ['email' => 'skip.mentor@example.test'])->assertSessionHasErrors('skills');
        $this->post('/mentee/create', $minimal + ['email' => 'skip.mentee@example.test'])->assertSessionHasErrors('career_goals');

        $this->assertFalse(DB::table('mentor_profile')->where('email', 'skip.mentor@example.test')->exists());
        $this->assertFalse(DB::table('mentee_profile')->where('email', 'skip.mentee@example.test')->exists());
    }

    public function test_staff_still_set_the_status_and_become_the_creator() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');

        $this->actingAs($accountManager)->post('/mentee/create', [
            'first_name' => 'Staff', 'last_name' => 'Made', 'year_of_birth' => '1995',
            'email' => 'staff.mentee@example.test', 'public_form' => 'false', 'status_id' => 2,
            'creator_user_id' => 999, 'cv_file_name' => 'forged.pdf',
        ])->assertSessionHasNoErrors();

        $row = DB::table('mentee_profile')->where('email', 'staff.mentee@example.test')->first();
        $this->assertSame(2, (int) $row->status_id);
        $this->assertSame($accountManager->id, (int) $row->creator_user_id);
        $this->assertNull($row->cv_file_name);
    }

    private function mentor(): array {
        $admin = $this->makeBackofficeUser(1, 'Admin');

        return [
            'first_name' => 'Guest', 'last_name' => 'Mentor', 'email' => 'guest.mentor@example.test',
            'year_of_birth' => '1980', 'residence_id' => $this->lookupId('residence'), 'reference_id' => $this->lookupId('reference'),
            'address' => 'Street 1', 'education_level_id' => $this->lookupId('education_level'), 'university_id' => $this->lookupId('university'),
            'company_id' => $this->makeCompany($admin, 'Company'), 'company_sector' => 'IT', 'job_position' => 'Lead',
            'job_experience_years' => '10', 'skills' => 'php', 'specialties' => [['id' => $this->lookupId('specialty')]],
            'industries' => [['id' => $this->lookupId('industry')]], 'public_form' => 'true',
        ];
    }

    private function mentee(): array {
        return [
            'first_name' => 'Guest', 'last_name' => 'Mentee', 'email' => 'guest.mentee@example.test',
            'year_of_birth' => '1999', 'residence_id' => $this->lookupId('residence'), 'reference_id' => $this->lookupId('reference'),
            'address' => 'Street 2', 'education_level_id' => $this->lookupId('education_level'), 'university_id' => $this->lookupId('university'),
            'university_department_name' => 'CS', 'university_graduation_year' => '2021', 'specialty_experience' => 'none',
            'specialties' => [['id' => $this->lookupId('specialty')]], 'expectations' => 'learn', 'career_goals' => 'dev', 'public_form' => 'true',
        ];
    }
}
