<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 5/31/17
 * Time: 4:06 PM
 */

namespace App\StorageLayer;


use App\Models\eloquent\MenteeRating;

class MenteeRatingStorage
{
    public function rateMentee($sessionId, $menteeId, $mentorId, $rating, $ratingDescription)
    {
        $menteeRating = new MenteeRating();
        $menteeRating->session_id = $sessionId;
        $menteeRating->mentee_id = $menteeId;
        $menteeRating->rated_by_id = $mentorId;
        $menteeRating->rating = $rating;
        $menteeRating->rating_description = $ratingDescription;
        $menteeRating->save();
        return $menteeRating;
    }

    public function getAverageRatingForMentee($menteeId)
    {
        $avgRating = MenteeRating::where('mentee_id', $menteeId)->avg('rating')->first();
        return $avgRating;
    }
}
