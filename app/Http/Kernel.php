<?php

namespace App\Http;

use App\Http\Middleware\CanCreateMentorshipSession;
use App\Http\Middleware\CanEditMentorsAndMentees;
use App\Http\Middleware\CanEditUserProfile;
use App\Http\Middleware\CanInviteMentee;
use App\Http\Middleware\CanOnlyChangeStatus;
use App\Http\Middleware\CanUpdateMentorshipSession;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\IsAccountManager;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\PreventRequestForgery;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel {
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        PreventRequestsDuringMaintenance::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            PreventRequestForgery::class,
            SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],

        'admin' => [
            IsAdmin::class,
        ],

        'status-changer' => [
            CanOnlyChangeStatus::class,
        ],

        'can-create-mentorship-session' => [
            CanCreateMentorshipSession::class,
        ],

        'account-manager' => [
            IsAccountManager::class,
        ],

        'can-update-sessions' => [
            CanUpdateMentorshipSession::class,
        ],

        'can-invite-mentee' => [
            CanInviteMentee::class,
        ],
        'can-edit-mentors-and-mentees' => [
            CanEditMentorsAndMentees::class,
        ],
        'can-edit-user-profile' => [
            CanEditUserProfile::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'bindings' => SubstituteBindings::class,
        'can' => Authorize::class,
        'guest' => RedirectIfAuthenticated::class,
        'throttle' => ThrottleRequests::class,
    ];
}
