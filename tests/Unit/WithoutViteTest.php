<?php

use Illuminate\Support\Facades\Vite;

/**
 * PHPUnit must not depend on a frontend build: views render without
 * public/build/manifest.json, also on a fresh checkout.
 */
class WithoutViteTest extends TestCase {
    public function test_a_page_renders_without_a_build_manifest() {
        // Neither a build nor a running dev server (public/hot) may be needed.
        Vite::useBuildDirectory('no-such-build');
        Vite::useHotFile(storage_path('no-such-hot-file'));

        $this->get('/login')->assertOk();
    }
}
