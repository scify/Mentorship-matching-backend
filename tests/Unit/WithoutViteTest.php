<?php

use Illuminate\Support\Facades\Vite;

/**
 * CI runs PHPUnit without `npm run build`, so views must render without
 * public/build/manifest.json.
 */
class WithoutViteTest extends TestCase
{
    public function testAPageRendersWithoutABuildManifest()
    {
        Vite::useBuildDirectory('no-such-build');

        $this->get('/login')->assertOk();
    }
}
