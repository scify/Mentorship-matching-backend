<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * The locale that the lang parameter asks for, when the app has its
     * translations (lang/en, lang/gr). Email links and the public forms pass
     * lang=gr; any other value, including a forged one, falls back to English.
     */
    protected function localeFromRequest(Request $request): string {
        $lang = $request->input('lang');

        return in_array($lang, ['en', 'gr'], true) ? $lang : 'en';
    }
}
