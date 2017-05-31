<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 5/30/17
 * Time: 5:36 PM
 */

namespace App\Http\Controllers;


class RatingController extends Controller
{
    public function showMenteeRatingForm($sessionId, $mentorId, $menteeId)
    {
        $ratedRole = 'mentee';
        return view('ratings.rating', compact('sessionId', 'mentorId', 'menteeId', 'ratedRole'));
    }

    public function  showMentorRatingForm($sessionId, $menteeId, $mentorId)
    {
        $ratedRole = 'mentor';
        return view('ratings.rating', compact('sessionId', 'mentorId', 'menteeId', 'ratedRole'));
    }
}
