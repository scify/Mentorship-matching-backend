<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets only the app itself frame a staff page, so another site cannot hide a
 * staff page in a frame and trick a logged-in user into clicking (clickjacking).
 * The public forms do not use it: another site embeds them.
 */
class SameOriginFrames {
    public function handle(Request $request, Closure $next): Response {
        $response = $next($request);
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");

        return $response;
    }
}
