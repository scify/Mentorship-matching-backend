<?php

class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        // Guests are redirected to the login screen.
        $this->get('/')->assertRedirect('/login');
    }
}
