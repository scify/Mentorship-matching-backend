<?php

/**
 * The session cookie carries SameSite=Lax, so another site cannot send it
 * with a cross-site POST.
 */
class SessionCookieTest extends TestCase {
    public function test_the_session_cookie_is_same_site_lax() {
        $cookie = collect($this->get('/login')->headers->getCookies())
            ->first(fn ($cookie) => $cookie->getName() === config('session.cookie'));

        $this->assertNotNull($cookie);
        $this->assertSame('lax', $cookie->getSameSite());
        $this->assertTrue($cookie->isHttpOnly());
    }
}
