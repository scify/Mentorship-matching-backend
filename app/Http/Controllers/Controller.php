<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\Rule;

class Controller extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Rules for an id that must belong to an active user with the account
     * manager role (role id 3).
     *
     * @return list<mixed>
     */
    protected function activeAccountManagerRules(): array {
        return [
            'integer',
            Rule::exists('users', 'id')->whereNull('deleted_at')->where('state_id', 1),
            Rule::exists('user_role', 'user_id')->where('role_id', 3)->whereNull('deleted_at'),
        ];
    }

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
