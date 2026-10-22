<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Proves that setUp() refuses the development database before RefreshDatabase
 * runs migrate:fresh. Nothing listens on 127.0.0.1:1, so without the guard the
 * migration fails with a QueryException instead of the guard's message.
 */
class TestDatabaseGuardSetUpTest extends TestCase
{
    use RefreshDatabase;

    private ?RuntimeException $setUpException = null;

    public function createApplication()
    {
        $app = parent::createApplication();

        $app['config']->set([
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '1',
            'database.connections.mysql.database' => 'db',
        ]);

        return $app;
    }

    protected function setUp(): void
    {
        try {
            parent::setUp();
        } catch (RuntimeException $exception) {
            $this->setUpException = $exception;
        }
    }

    public function testSetUpRefusesTheDevelopmentDatabaseBeforeMigrating()
    {
        $this->assertNotNull($this->setUpException);
        $this->assertStringStartsWith(
            'Tests must use the MariaDB database [testing], not [mysql] database [db]',
            $this->setUpException->getMessage()
        );
    }
}
