<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 5/31/17
 * Time: 4:06 PM
 */

namespace App\StorageLayer;


use App\Models\eloquent\MentorRating;

class MentorRatingStorage
{
    public function rateMentor($sessionId, $mentorId, $menteeId, $rating, $ratingDescription)
    {
        $mentorRating = new MentorRating();
        $mentorRating->session_id = $sessionId;
        $mentorRating->mentor_id = $mentorId;
        $mentorRating->rated_by_id = $menteeId;
        $mentorRating->rating = $rating;
        $mentorRating->rating_description = $ratingDescription;
        $mentorRating->save();
        return $mentorRating;
    }

    public function getAverageRatingForMentor($mentorId)
    {
        $avgRating = MentorRating::where('mentor_id', $mentorId)->avg('rating')->first();
        return $avgRating;
    }
}
