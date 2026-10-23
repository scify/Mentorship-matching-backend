<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

/**
 * CVs hold personal data. They are stored outside the public disk, under a
 * random name, and only staff download them through the app.
 */
class CvFileTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        Notification::fake();
        Storage::fake('local');
        Storage::fake('public');
    }

    public function test_an_uploaded_cv_is_stored_privately_under_a_random_name() {
        $this->post('/mentee/create', $this->mentee() + ['cv_file' => UploadedFile::fake()->create('cv.pdf', 20, 'application/pdf')])
            ->assertSessionHasNoErrors();

        $name = DB::table('mentee_profile')->where('email', 'cv.mentee@example.test')->value('cv_file_name');
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{40}\.pdf$/', (string) $name);
        Storage::disk('local')->assertExists('private/cv_files/' . $name);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_staff_download_a_private_cv_and_guests_cannot() {
        $admin = $this->makeBackofficeUser(1, 'Admin');
        Storage::disk('local')->put('private/cv_files/abc.pdf', 'cv');
        $menteeId = $this->makeMenteeProfile($admin, ['cv_file_name' => 'abc.pdf']);

        $this->get("/mentee/{$menteeId}/cv")->assertRedirect('/login');
        $this->actingAs($admin)->get("/mentee/{$menteeId}/cv")->assertOk()->assertDownload('abc.pdf');
    }

    public function test_a_cv_from_before_the_move_still_downloads() {
        $admin = $this->makeBackofficeUser(1, 'Admin');
        Storage::disk('public')->put('uploads/cv_files/old.pdf', 'cv');
        $mentorId = $this->makeMentorProfile($admin, $this->makeCompany($admin, 'Company'), ['cv_file_name' => 'old.pdf']);

        $this->actingAs($admin)->get("/mentor/{$mentorId}/cv")->assertOk()->assertDownload('old.pdf');
    }

    public function test_a_file_name_cannot_leave_the_cv_folders() {
        $admin = $this->makeBackofficeUser(1, 'Admin');
        // private/cv_files/../secret.txt would resolve to private/secret.txt without basename().
        Storage::disk('local')->put('private/secret.txt', 'secret');
        $this->actingAs($admin);

        foreach (['../secret.txt', '..', '.'] as $name) {
            $menteeId = $this->makeMenteeProfile($admin, ['cv_file_name' => $name]);
            $this->get("/mentee/{$menteeId}/cv")->assertNotFound();
        }
    }

    public function test_the_move_command_moves_old_cvs_to_private_storage() {
        Storage::disk('public')->put('uploads/cv_files/old.pdf', 'old cv');
        Storage::disk('public')->put('uploads/cv_files/.gitignore', '*');

        $this->artisan('cv:move-to-private')->assertSuccessful();
        $this->artisan('cv:move-to-private')->assertSuccessful();

        Storage::disk('public')->assertMissing('uploads/cv_files/old.pdf');
        Storage::disk('public')->assertExists('uploads/cv_files/.gitignore');
        $this->assertSame('old cv', Storage::disk('local')->get('private/cv_files/old.pdf'));
    }

    private function mentee(): array {
        return [
            'first_name' => 'Cv', 'last_name' => 'Mentee', 'email' => 'cv.mentee@example.test',
            'year_of_birth' => '1999', 'residence_id' => $this->lookupId('residence'), 'reference_id' => $this->lookupId('reference'),
            'address' => 'Street 2', 'education_level_id' => $this->lookupId('education_level'), 'university_id' => $this->lookupId('university'),
            'university_department_name' => 'CS', 'university_graduation_year' => '2021', 'specialty_experience' => 'none',
            'specialties' => [['id' => $this->lookupId('specialty')]], 'expectations' => 'learn', 'career_goals' => 'dev', 'public_form' => 'true',
        ];
    }
}
