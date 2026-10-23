<?php

use Illuminate\Support\Facades\DB;

/**
 * The guard in TestCase lets the tests touch only the dedicated `testing`
 * database, never the developer's `db` database or another engine.
 */
class TestDatabaseGuardTest extends TestCase {
    public function test_the_testing_database_is_accepted() {
        $this->ensureTestDatabaseIsTheTestingDatabase();

        $this->addToAssertionCount(1);
    }

    public function test_the_development_database_is_refused() {
        config(['database.connections.mysql.database' => 'db']);
        DB::purge('mysql');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tests must use the MariaDB database [testing], not [mysql] database [db]');

        $this->ensureTestDatabaseIsTheTestingDatabase();
    }

    public function test_another_connection_is_refused() {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => 'testing']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tests must use the MariaDB database [testing], not [sqlite] database [testing]');

        $this->ensureTestDatabaseIsTheTestingDatabase();
    }
}
