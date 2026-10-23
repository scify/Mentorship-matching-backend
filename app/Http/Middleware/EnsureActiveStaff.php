<?php

namespace App\Http\Middleware;

use App\Models\eloquent\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Runs after `auth` on every protected route. Login already requires an
 * active user, but a live session, a remember-me cookie or a password reset
 * would keep a user logged in after an admin deactivates them or removes
 * their roles. Such a user is logged out here.
 */
class EnsureActiveStaff {
    public function handle(Request $request, Closure $next): Response {
        $user = $request->user();

        if ($user instanceof User && $user->isActiveStaff()) {
            return $next($request);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // The page scripts parse AJAX responses as JSON; a redirect would hand them the login page.
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['message' => trans('auth.inactive')], 401);
        }

        return redirect()->route('login')->withErrors(['email_address' => trans('auth.inactive')]);
    }
}
