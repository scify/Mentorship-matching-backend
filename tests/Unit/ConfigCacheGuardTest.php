<?php

/**
 * A cached config replaces every <server> line in phpunit.xml, so the tests
 * would use the .env database, mailer and Sentry DSN.
 */
class ConfigCacheGuardTest extends TestCase {
    private string $cachedConfigPath;

    protected function setUp(): void {
        parent::setUp();

        $this->cachedConfigPath = tempnam(sys_get_temp_dir(), 'config-cache');
        file_put_contents($this->cachedConfigPath, '<?php return [];');
        $_SERVER['APP_CONFIG_CACHE'] = $this->cachedConfigPath;
    }

    protected function tearDown(): void {
        unset($_SERVER['APP_CONFIG_CACHE']);
        unlink($this->cachedConfigPath);

        parent::tearDown();
    }

    public function test_a_cached_config_is_refused() {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tests cannot run with a cached config');

        $this->createApplication();
    }
}
