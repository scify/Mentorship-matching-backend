<?php

use Illuminate\Support\Facades\DB;

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * RefreshDatabase seeds only on the first migration of a run, and the
     * fixtures need the seeded lookup tables, so every test asks for it.
     *
     * @var bool
     */
    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        // Views render without public/build/manifest.json.
        $this->withoutVite();
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        // A cached config replaces every <server> line in phpunit.xml.
        if (file_exists($app->getCachedConfigPath())) {
            throw new RuntimeException('Tests cannot run with a cached config. Run "php artisan config:clear".');
        }

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * RefreshDatabase wipes the database, and any test can write to it, so
     * refuse everything except the dedicated `testing` database before the
     * traits run.
     */
    protected function setUpTraits()
    {
        $this->ensureTestDatabaseIsTheTestingDatabase();

        return parent::setUpTraits();
    }

    protected function ensureTestDatabaseIsTheTestingDatabase(): void
    {
        $connection = DB::connection();
        $driver = $connection->getConfig('driver');
        $database = $connection->getConfig('database');

        if (!in_array($driver, ['mysql', 'mariadb'], true) || $database !== 'testing') {
            throw new RuntimeException(
                "Tests must use the MariaDB database [testing], not [{$driver}] database [{$database}]. "
                .'Check the DB_* lines in phpunit.xml.'
            );
        }
    }
}
