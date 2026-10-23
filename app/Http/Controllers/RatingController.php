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

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class RatingController extends Controller {
    public function showMenteeRatingForm($sessionId, $mentorId, $menteeId, Request $request) {
        $ratedRole = 'mentee';
        $lang = $this->localeFromRequest($request);
        App::setLocale($lang);
        $mentorshipSessionManager = new MentorshipSessionManager;
        $session = $mentorshipSessionManager->getMentorshipSession($sessionId);
        if (!empty($session)) {
            if ($session->status_id === MentorshipSessionStatuses::getCompletedSessionStatuses()[0] &&
                $session->mentor_profile_id == $mentorId && $session->mentee_profile_id == $menteeId) {
                $sessionViewModel = $mentorshipSessionManager->getMentorshipSessionViewModel(
                    $session
                );

                // The form posts back to the signed URL of this page.
                $formAction = $request->fullUrl();

                return view('ratings.rating', compact('ratedRole', 'sessionViewModel', 'lang', 'formAction'));
            } else {
                return view('common.response-to-email')->with([
                    'message_failure' => Lang::get('messages.rate_mentee_permission'),
                    'title' => Lang::get('messages.rate_mentee'),
                ]);
            }
        } else {
            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.invalid_operation'),
                'title' => Lang::get('messages.rate_mentee'),
            ]);
        }
    }

    public function rateMentee(string $sessionId, string $mentorId, string $menteeId, Request $request) {
        $lang = $this->localeFromRequest($request);
        App::setLocale($lang);
        $this->validate($request, [
            'rating' => 'required|min:1|max:5|numeric',
        ], $this->messages());
        $ratingManager = new RatingManager;
        try {
            $result = $ratingManager->rateMentee((int) $sessionId, (int) $mentorId, (int) $menteeId, (int) $request->input('rating'), (string) $request->input('rating_description'));
        } catch (Exception $e) {
            Log::info('Error while rating mentee: ' . $e);

            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.rate_mentee_more_than_once'),
                'title' => Lang::get('messages.rate_mentee'),
            ]);
        }
        if ($result === 'SUCCESS') {
            return view('common.response-to-email')->with([
                'message_success' => Lang::get('messages.thank_you_for_rating'),
                'title' => Lang::get('messages.rate_mentee'),
            ]);
        } else {
            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.rate_mentee_permission_denied'),
                'title' => Lang::get('messages.rate_mentee'),
            ]);
        }
    }

    public function showMentorRatingForm($sessionId, $menteeId, $mentorId, Request $request) {
        $ratedRole = 'mentor';
        $lang = $this->localeFromRequest($request);
        App::setLocale($lang);
        $mentorshipSessionManager = new MentorshipSessionManager;
        $session = $mentorshipSessionManager->getMentorshipSession($sessionId);
        if (!empty($session)) {
            if ($session->status_id === MentorshipSessionStatuses::getCompletedSessionStatuses()[0] &&
                $session->mentor_profile_id == $mentorId && $session->mentee_profile_id == $menteeId) {
                $sessionViewModel = $mentorshipSessionManager->getMentorshipSessionViewModel(
                    $session
                );

                // The form posts back to the signed URL of this page.
                $formAction = $request->fullUrl();

                return view('ratings.rating', compact('ratedRole', 'sessionViewModel', 'lang', 'formAction'));
            } else {
                return view('common.response-to-email')->with([
                    'message_failure' => Lang::get('messages.rate_mentor_permission'),
                    'title' => Lang::get('messages.rate_mentor'),
                ]);
            }
        } else {
            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.invalid_operation'),
                'title' => Lang::get('messages.rate_mentor'),
            ]);
        }
    }

    public function rateMentor(string $sessionId, string $menteeId, string $mentorId, Request $request) {
        $lang = $this->localeFromRequest($request);
        App::setLocale($lang);
        $this->validate($request, [
            'rating' => 'required|min:1|max:5|numeric',
        ], $this->messages());
        $ratingManager = new RatingManager;
        try {
            $result = $ratingManager->rateMentor((int) $sessionId, (int) $mentorId, (int) $menteeId, (int) $request->input('rating'), (string) $request->input('rating_description'));
        } catch (Exception $e) {
            Log::info('Error while rating mentor: ' . $e);

            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.rate_mentor_more_than_once'),
                'title' => Lang::get('messages.rate_mentor'),
            ]);
        }
        if ($result === 'SUCCESS') {
            return view('common.response-to-email')->with([
                'message_success' => Lang::get('messages.thank_you_for_rating'),
                'title' => Lang::get('messages.rate_mentor'),
            ]);
        } else {
            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.rate_mentor_permission_denied'),
                'title' => Lang::get('messages.rate_mentor'),
            ]);
        }
    }

    private function messages() {
        return [
            'rating.required' => trans('messages.rating.required'),
            'rating.min' => trans('messages.rating.error'),
            'rating.max' => trans('messages.rating.error'),
            'rating.numeric' => trans('messages.rating.error'),
        ];
    }
}
