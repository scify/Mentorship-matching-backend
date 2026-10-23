<?php

use Illuminate\Support\Facades\Lang;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The session invitation emails link with lang=gr. The pages behind those
 * links must render in that language.
 */
class EmailLinkLocaleTest extends TestCase {
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
}
