<?php

use App\Models\eloquent\MentorshipSession;
use App\Notifications\MenteeSendRating;
use App\Notifications\MentorSendRating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

/**
 * The rating mails carry signed links. Only the recipient of a mail can open
 * its form and rate; the ids come from the signed path, never from the body.
 */
class RatingLinkTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    private const EVALUATION_SENT = 10;

    public function test_the_mentor_rates_the_mentee_through_the_signed_link() {
        $session = $this->makeSession();
        $url = $this->menteeRatingUrl($session);

        $this->get($url)->assertOk()->assertSee('ratingForm', false);
        $this->post($url, ['rating' => 4, 'rating_description' => 'good'])->assertOk();

        $this->assertSame(4, (int) DB::table('mentee_rating')->where('session_id', $session->id)->value('rating'));
    }

    public function test_an_unsigned_or_altered_link_is_refused() {
        $session = $this->makeSession();
        $plain = "/rateMentee/{$session->id}/{$session->mentor_profile_id}/{$session->mentee_profile_id}";

        $this->get($plain)->assertForbidden();
        $this->post($plain, ['rating' => 1, 'rating_description' => ''])->assertForbidden();
        $this->post($this->menteeRatingUrl($session) . '&lang=en', ['rating' => 1, 'rating_description' => ''])->assertForbidden();

        $this->assertSame(0, DB::table('mentee_rating')->count());
    }

    public function test_a_mentor_cannot_use_their_link_to_rate_themselves() {
        $session = $this->makeSession();
        // The mentor's mail links to the mentee rating form; swap the path to the mentor rating form.
        $forged = str_replace('/rateMentee/', '/rateMentor/', $this->menteeRatingUrl($session));

        $this->post($forged, ['rating' => 5, 'rating_description' => 'self'])->assertForbidden();

        $this->assertSame(0, DB::table('mentor_rating')->count());
    }

    public function test_the_old_body_id_routes_are_gone() {
        $session = $this->makeSession();

        $this->post('/rateMentor', [
            'session_id' => $session->id, 'mentor_id' => $session->mentor_profile_id, 'mentee_id' => $session->mentee_profile_id, 'rating' => 5,
        ])->assertNotFound();

        $this->assertSame(0, DB::table('mentor_rating')->count());
    }

    public function test_the_rating_mails_carry_signed_links() {
        $session = $this->makeSession();

        $mentorMail = (new MentorSendRating($session))->toMail($session->mentor)->actionUrl;
        $menteeMail = (new MenteeSendRating($session))->toMail($session->mentee)->actionUrl;

        $this->assertStringStartsWith(rtrim(config('app.url'), '/') . "/rateMentee/{$session->id}/{$session->mentor_profile_id}/{$session->mentee_profile_id}?", $mentorMail);
        $this->assertStringStartsWith(rtrim(config('app.url'), '/') . "/rateMentor/{$session->id}/{$session->mentee_profile_id}/{$session->mentor_profile_id}?", $menteeMail);
        $this->get($mentorMail)->assertOk()->assertSee('ratingForm', false);
        $this->get($menteeMail)->assertOk()->assertSee('ratingForm', false);
    }

    public function test_the_mentee_rates_the_mentor_through_the_link_in_their_mail() {
        $session = $this->makeSession();
        $url = (new MenteeSendRating($session))->toMail($session->mentee)->actionUrl;

        $this->post($url, ['rating' => 5, 'rating_description' => 'great'])->assertOk();

        $this->assertSame(5, (int) DB::table('mentor_rating')->where('session_id', $session->id)->value('rating'));
    }

    public function test_the_link_works_on_another_host_name_or_scheme() {
        $session = $this->makeSession();
        $path = parse_url((new MentorSendRating($session))->toMail($session->mentor)->actionUrl);

        // Behind a TLS proxy the app may see http and another host; the signature covers path and query only.
        $this->get('http://internal.example' . $path['path'] . '?' . $path['query'])->assertOk();
    }

    private function makeSession(): MentorshipSession {
        $admin = $this->makeBackofficeUser(1, 'Admin');

        return $this->makeSessionAtStatus(
            $this->makeMentorProfile($admin, $this->makeCompany($admin, 'Company')),
            $this->makeMenteeProfile($admin),
            $admin, $admin, self::EVALUATION_SENT,
        );
    }

    private function menteeRatingUrl(MentorshipSession $session): string {
        return rtrim(config('app.url'), '/') . URL::temporarySignedRoute('showMenteeRatingForm', now()->addDays(30), [
            'sessionId' => $session->id, 'mentorId' => $session->mentor_profile_id, 'menteeId' => $session->mentee_profile_id, 'lang' => 'gr',
        ], false);
    }
}
