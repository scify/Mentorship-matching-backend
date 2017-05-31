<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 5/30/17
 * Time: 5:36 PM
 */

namespace App\Http\Controllers;


use App\BusinessLogicLayer\managers\MentorshipSessionManager;
use App\BusinessLogicLayer\managers\RatingManager;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RatingController extends Controller
{
    public function showMenteeRatingForm($sessionId, $mentorId, $menteeId)
    {
        $ratedRole = 'mentee';
        $mentorshipSessionManager = new MentorshipSessionManager();
        try {
            $sessionViewModel = $mentorshipSessionManager->getMentorshipSessionViewModel(
                $mentorshipSessionManager->getMentorshipSession($sessionId)
            );
        } catch(Exception $e) {
            Log::info("Error on show rating form for mentee: " . $e);
            return view('common.response-to-email')->with([
                'message_failure' => 'Invalid operation.',
                'title' => 'Rate your mentee'
            ]);
        }
        return view('ratings.rating', compact('sessionId', 'mentorId', 'menteeId', 'ratedRole', 'sessionViewModel'));
    }

    public function rateMentee(Request $request)
    {
        $this->validate($request, [
            'rating' => 'required|min:1|max:5|numeric'
        ]);
        $input = $request->all();
        $ratingManager = new RatingManager();
        try {
            $result = $ratingManager->rateMentee($input);
        } catch(Exception $e) {
            Log::info("Error while rating mentee: " . $e);
            return view('common.response-to-email')->with([
                'message_failure' => 'You cannot rate more than once a mentee for a session.',
                'title' => 'Rate your mentee'
            ]);
        }
        if ($result === 'SUCCESS') {
            return view('common.response-to-email')->with([
                'message_success' => 'Thank you for rating!',
                'title' => 'Rate your mentee'
            ]);
        } else {
            return view('common.response-to-email')->with([
                'message_failure' => 'Permissions denied. You cannot rate this mentee.',
                'title' => 'Rate your mentee'
            ]);
        }
    }

    public function  showMentorRatingForm($sessionId, $menteeId, $mentorId)
    {
        $ratedRole = 'mentor';
        $mentorshipSessionManager = new MentorshipSessionManager();
        try {
            $sessionViewModel = $mentorshipSessionManager->getMentorshipSessionViewModel(
                $mentorshipSessionManager->getMentorshipSession($sessionId)
            );
        } catch(Exception $e) {
            Log::info("Error on show rating form for mentor: " . $e);
            return view('common.response-to-email')->with([
                'message_failure' => 'Invalid operation.',
                'title' => 'Rate your mentor'
            ]);
        }
        return view('ratings.rating', compact('sessionId', 'mentorId', 'menteeId', 'ratedRole', 'sessionViewModel'));
    }

    public function rateMentor(Request $request)
    {
        $this->validate($request, [
            'rating' => 'required|min:1|max:5|numeric'
        ]);
        $input = $request->all();
        $ratingManager = new RatingManager();
        try {
            $result = $ratingManager->rateMentor($input);
        } catch(Exception $e) {
            Log::info("Error while rating mentor: " . $e);
            return view('common.response-to-email')->with([
                'message_failure' => 'You cannot rate more than once a mentor for a session.',
                'title' => 'Rate your mentor'
            ]);
        }
        if ($result === 'SUCCESS') {
            return view('common.response-to-email')->with([
                'message_success' => 'Thank you for rating!',
                'title' => 'Rate your mentor'
            ]);
        } else {
            return view('common.response-to-email')->with([
                'message_failure' => 'Permissions denied. You cannot rate this mentor.',
                'title' => 'Rate your mentor'
            ]);
        }
    }
}
