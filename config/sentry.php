<?php

return [

    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),

    // capture release as git sha
    // 'release' => trim(exec('git --git-dir ' . base_path('.git') . ' log --pretty="%h" -n1 HEAD')),

    'breadcrumbs' => [

        // SQL bindings hold personal data (names, emails); send them only when a server opts in.
        'sql_bindings' => env('SENTRY_SQL_BINDINGS', false),

    ],

];
