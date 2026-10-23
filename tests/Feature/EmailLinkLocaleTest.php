<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The session invitation emails link with lang=gr. The pages behind those
 * links must render in that language.
 */
class EmailLinkLocaleTest extends TestCase {
    use RefreshDatabase;

    public static function emailLinkPages(): array {
        return [
            'decline confirmation' => ['/decline-session-confirmation/0/mentee/0/nobody@example.test', 'messages.decline_session_invitation_second_title'],
            'accept' => ['/accept-session/0/mentee/0/nobody@example.test', 'messages.response_to_session_invitation'],
            'decline' => ['/decline-session/0/mentee/0/nobody@example.test', 'messages.response_to_session_invitation'],
        ];
    }

    #[DataProvider('emailLinkPages')]
    public function test_the_page_renders_in_the_language_of_the_link(string $path, string $key) {
        $this->get($path . '?lang=gr')
            ->assertOk()
            ->assertSee(Lang::get($key, [], 'gr'), false);
    }

    #[DataProvider('emailLinkPages')]
    public function test_the_page_renders_in_english_without_a_language(string $path, string $key) {
        $this->get($path)
            ->assertOk()
            ->assertSee(Lang::get($key, [], 'en'), false);
    }

    public static function forgedLanguages(): array {
        return [
            'array' => ['lang[]=gr'],
            'path' => ['lang=../gr'],
            'unknown' => ['lang=fr'],
        ];
    }

    /**
     * A forged lang value falls back to English instead of failing.
     */
    #[DataProvider('forgedLanguages')]
    public function test_a_forged_language_falls_back_to_english(string $query) {
        $this->get('/decline-session-confirmation/0/mentee/0/nobody@example.test?' . $query)
            ->assertOk()
            ->assertSee(Lang::get('messages.decline_session_invitation_second_title', [], 'en'), false);
        $this->get('/mentor/create?' . $query)->assertOk();
        $this->get('/mentee/create?' . $query)->assertOk();
        // The rating pages need a signed URL, which covers the query string, so lang cannot be forged there.
    }
}
