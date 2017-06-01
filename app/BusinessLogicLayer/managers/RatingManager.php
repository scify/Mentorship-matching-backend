<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 5/31/17
 * Time: 3:49 PM
 */

namespace App\BusinessLogicLayer\managers;


use App\StorageLayer\MentorRatingStorage;
use App\StorageLayer\MentorshipSessionStorage;
use App\StorageLayer\MenteeRatingStorage;
use App\Utils\MentorshipSessionStatuses;

class RatingManager
{
    private $menteeRatingStorage;
    private $mentorRatingStorage;

    public function __construct()
    {
        $this->menteeRatingStorage = new MenteeRatingStorage();
        $this->mentorRatingStorage = new MentorRatingStorage();
    }

    public function rateMentee(array $input)
    {
        $sessionStorage = new MentorshipSessionStorage();
        $session = $sessionStorage->findMentorshipSessionById($input['session_id']);
        $sessionStatuses = new MentorshipSessionStatuses();
        if ($session->status_id === $sessionStatuses::getCompletedSessionStatuses()[0] &&
            $session->mentor_profile_id == $input['mentor_id'] && $session->mentee_profile_id == $input['mentee_id']) {
            $this->menteeRatingStorage->rateMentee(
                $session->id, $session->mentee_profile_id, $session->mentor_profile_id, $input['rating'], $input['rating_description']
            );
            return 'SUCCESS';
        } else {
            return 'FAIL';
        }
    }

    public function rateMentor(array $input)
    {
        $sessionStorage = new MentorshipSessionStorage();
        $session = $sessionStorage->findMentorshipSessionById($input['session_id']);
        $sessionStatuses = new MentorshipSessionStatuses();
        if ($session->status_id === $sessionStatuses::getCompletedSessionStatuses()[0] &&
            $session->mentor_profile_id == $input['mentor_id'] && $session->mentee_profile_id == $input['mentee_id']) {
            $this->mentorRatingStorage->rateMentor(
                $session->id, $session->mentor_profile_id, $session->mentee_profile_id, $input['rating'], $input['rating_description']
            );
            return 'SUCCESS';
        } else {
            return 'FAIL';
        }
    }
}
