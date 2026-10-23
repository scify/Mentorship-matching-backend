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
        // Neither a build nor a running dev server (public/hot) may be needed.
        Vite::useBuildDirectory('no-such-build');
        Vite::useHotFile(storage_path('no-such-hot-file'));

        $this->get('/login')->assertOk();
    }
}
