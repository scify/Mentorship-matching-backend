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
use App\Utils\MentorshipSessionStatuses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class RatingController extends Controller
{
    public function showMenteeRatingForm($sessionId, $mentorId, $menteeId)
    {
        $ratedRole = 'mentee';
        $lang = Input::has('lang') ? Input::get('lang') : 'en';
        App::setLocale($lang);
        $mentorshipSessionManager = new MentorshipSessionManager();
        $session = $mentorshipSessionManager->getMentorshipSession($sessionId);
        if (!empty($session)) {
            if ($session->status_id === MentorshipSessionStatuses::getCompletedSessionStatuses()[0] &&
                $session->mentor_profile_id == $mentorId && $session->mentee_profile_id == $menteeId) {
                $sessionViewModel = $mentorshipSessionManager->getMentorshipSessionViewModel(
                    $session
                );
                return view('ratings.rating', compact('sessionId', 'mentorId', 'menteeId', 'ratedRole', 'sessionViewModel', 'lang'));
            } else {
                return view('common.response-to-email')->with([
                    'message_failure' => Lang::get('messages.rate_mentee_permission'),
                    'title' => Lang::get('messages.rate_mentee')
                ]);
            }
        } else {
            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.invalid_operation'),
                'title' => Lang::get('messages.rate_mentee')
            ]);
        }
    }

    public function rateMentee(Request $request)
    {
        $lang = Input::has('lang') ? Input::get('lang') : 'en';
        App::setLocale($lang);
        $this->validate($request, [
            'rating' => 'required|min:1|max:5|numeric'
        ], $this->messages());
        $input = $request->all();
        $ratingManager = new RatingManager();
        try {
            $result = $ratingManager->rateMentee($input);
        } catch(Exception $e) {
            Log::info("Error while rating mentee: " . $e);
            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.rate_mentee_more_than_once'),
                'title' => Lang::get('messages.rate_mentee')
            ]);
        }
        if ($result === 'SUCCESS') {
            return view('common.response-to-email')->with([
                'message_success' => Lang::get('messages.thank_you_for_rating'),
                'title' => Lang::get('messages.rate_mentee')
            ]);
        } else {
            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.rate_mentee_permission_denied'),
                'title' => Lang::get('messages.rate_mentee')
            ]);
        }
    }

    public function showMentorRatingForm($sessionId, $menteeId, $mentorId)
    {
        $ratedRole = 'mentor';
        $lang = Input::has('lang') ? Input::get('lang') : 'en';
        App::setLocale($lang);
        $mentorshipSessionManager = new MentorshipSessionManager();
        $session = $mentorshipSessionManager->getMentorshipSession($sessionId);
        if(!empty($session)) {
            if ($session->status_id === MentorshipSessionStatuses::getCompletedSessionStatuses()[0] &&
                $session->mentor_profile_id == $mentorId && $session->mentee_profile_id == $menteeId) {
                $sessionViewModel = $mentorshipSessionManager->getMentorshipSessionViewModel(
                    $session
                );
                return view('ratings.rating', compact('sessionId', 'mentorId', 'menteeId', 'ratedRole', 'sessionViewModel', 'lang'));
            } else {
                return view('common.response-to-email')->with([
                    'message_failure' => Lang::get('messages.rate_mentor_permission'),
                    'title' => Lang::get('messages.rate_mentor')
                ]);
            }
        } else {
            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.invalid_operation'),
                'title' => Lang::get('messages.rate_mentor')
            ]);
        }
    }

    public function rateMentor(Request $request)
    {
        $lang = Input::has('lang') ? Input::get('lang') : 'en';
        App::setLocale($lang);
        $this->validate($request, [
            'rating' => 'required|min:1|max:5|numeric'
        ], $this->messages());
        $input = $request->all();
        $ratingManager = new RatingManager();
        try {
            $result = $ratingManager->rateMentor($input);
        } catch(Exception $e) {
            Log::info("Error while rating mentor: " . $e);
            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.rate_mentor_more_than_once'),
                'title' => Lang::get('messages.rate_mentor')
            ]);
        }
        if ($result === 'SUCCESS') {
            return view('common.response-to-email')->with([
                'message_success' => Lang::get('messages.thank_you_for_rating'),
                'title' => Lang::get('messages.rate_mentor')
            ]);
        } else {
            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.rate_mentor_permission_denied'),
                'title' => Lang::get('messages.rate_mentor')
            ]);
        }
    }

    private function messages()
    {
        return [
            'rating.required' => trans('messages.rating.required'),
            'rating.min' => trans('messages.rating.error'),
            'rating.max' => trans('messages.rating.error'),
            'rating.numeric' => trans('messages.rating.error'),
        ];
    }
}
