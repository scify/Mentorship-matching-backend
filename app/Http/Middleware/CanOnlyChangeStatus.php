<?php

namespace App\Http\Middleware;

use App\BusinessLogicLayer\managers\UserAccessManager;
use Closure;
use Illuminate\Support\Facades\Auth;

class CanOnlyChangeStatus
{

    private $userAccessManager;

    public function __construct() {
        $this->userAccessManager = new UserAccessManager();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if($this->userAccessManager->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees($user))
            return $next($request);
        return back()->withErrors(['You have no access to this page']);
    }
}
