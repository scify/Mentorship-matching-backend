<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 5/31/17
 * Time: 3:49 PM
 */

namespace App\BusinessLogicLayer\managers;


use App\StorageLayer\MentorshipSessionStorage;
use App\StorageLayer\RatingStorage;
use App\Utils\MentorshipSessionStatuses;

class RatingManager
{
    private $ratingStorage;

    public function __construct()
    {
        $this->ratingStorage = new RatingStorage();
    }

    public function rateMentee(array $input)
    {
        $sessionStorage = new MentorshipSessionStorage();
        $session = $sessionStorage->findMentorshipSessionById($input['session_id']);
        $sessionStatuses = new MentorshipSessionStatuses();
        if ($session->status_id === $sessionStatuses::getCompletedSessionStatuses()[0] &&
            $session->mentor_profile_id == $input['mentor_id'] && $session->mentee_profile_id == $input['mentee_id']) {
            $this->ratingStorage->rateMentee(
                $session->id, $session->mentee_profile_id, $session->mentor_profile_id, $input['rating']
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
            $this->ratingStorage->rateMentor(
                $session->id, $session->mentor_profile_id, $session->mentee_profile_id, $input['rating']
            );
            return 'SUCCESS';
        } else {
            return 'FAIL';
        }
    }
}
