<?php

class ExampleTest extends TestCase {
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function test_basic_example() {
        // Guests are redirected to the login screen.
        $this->get('/')->assertRedirect('/login');
    }
}
