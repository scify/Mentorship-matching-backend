<?php

namespace App\Http\Middleware;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * The response of a failed role check. back() would follow the Referer header,
 * so a link on another site could send a staff user to that site (open
 * redirect). The session's previous URL is always one of the app's own pages.
 */
trait DeniesAccess {
    protected function denyAccess(Request $request): RedirectResponse {
        $previousUrl = $request->hasSession() ? $request->session()->previousUrl() : null;

        return redirect()->to($previousUrl ?? route('home'))->withErrors(['You have no access to this page']);
    }
}
