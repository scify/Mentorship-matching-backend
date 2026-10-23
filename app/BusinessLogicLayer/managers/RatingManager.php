<?php

/**
 * Created by PhpStorm.
 * User: snik
 * Date: 5/31/17
 * Time: 3:49 PM
 */

namespace App\BusinessLogicLayer\managers;

use App\StorageLayer\MenteeRatingStorage;
use App\StorageLayer\MentorRatingStorage;
use App\StorageLayer\MentorshipSessionStorage;
use App\Utils\MentorshipSessionStatuses;

class RatingManager {
    private $menteeRatingStorage;

    private $mentorRatingStorage;

    public function __construct() {
        $this->menteeRatingStorage = new MenteeRatingStorage;
        $this->mentorRatingStorage = new MentorRatingStorage;
    }

    /**
     * The ids come from the signed rating link, so they belong to the mail's recipient.
     */
    public function rateMentee(int $sessionId, int $mentorId, int $menteeId, int $rating, string $ratingDescription): string {
        $session = (new MentorshipSessionStorage)->findMentorshipSessionById($sessionId);
        if ($session !== null && $session->status_id === MentorshipSessionStatuses::getCompletedSessionStatuses()[0] &&
            (int) $session->mentor_profile_id === $mentorId && (int) $session->mentee_profile_id === $menteeId) {
            $this->menteeRatingStorage->rateMentee($session->id, $session->mentee_profile_id, $session->mentor_profile_id, $rating, $ratingDescription);

            return 'SUCCESS';
        }

        return 'FAIL';
    }

    /**
     * The ids come from the signed rating link, so they belong to the mail's recipient.
     */
    public function rateMentor(int $sessionId, int $mentorId, int $menteeId, int $rating, string $ratingDescription): string {
        $session = (new MentorshipSessionStorage)->findMentorshipSessionById($sessionId);
        if ($session !== null && $session->status_id === MentorshipSessionStatuses::getCompletedSessionStatuses()[0] &&
            (int) $session->mentor_profile_id === $mentorId && (int) $session->mentee_profile_id === $menteeId) {
            $this->mentorRatingStorage->rateMentor($session->id, $session->mentor_profile_id, $session->mentee_profile_id, $rating, $ratingDescription);

            return 'SUCCESS';
        }

        return 'FAIL';
    }
}
