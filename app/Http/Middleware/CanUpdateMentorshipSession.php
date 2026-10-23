<?php

namespace App\Http\Middleware;

use App\BusinessLogicLayer\managers\UserAccessManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanUpdateMentorshipSession {
    use DeniesAccess;

    private $userAccessManager;

    public function __construct() {
        $this->userAccessManager = new UserAccessManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $user = Auth::user();
        if ($this->userAccessManager->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees($user) ||
            $this->userAccessManager->userIsMatcher($user) || $this->userAccessManager->userHasAccessToCRUDMentorshipSessions($user)) {
            return $next($request);
        }

        return $this->denyAccess($request);
    }
}
