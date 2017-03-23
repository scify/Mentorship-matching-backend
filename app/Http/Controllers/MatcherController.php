<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatcherController extends Controller
{

    /**
     * Display the matches a matcher is currently rolled in.
     *
     * @param  int  $matcherId
     * @return \Illuminate\Http\Response
     */
    public function showMatches($matcherId)
    {
        dd($matcherId);
    }

}
