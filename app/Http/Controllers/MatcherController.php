<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class MatcherController extends Controller {
    /**
     * Display the matches a matcher is currently rolled in.
     *
     * @param  int  $matcherId
     * @return Response
     */
    public function showMatches($matcherId) {
        dd($matcherId);
    }
}
