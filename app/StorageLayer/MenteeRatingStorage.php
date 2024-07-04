<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 5/31/17
 * Time: 4:06 PM
 */

namespace App\StorageLayer;


use App\Models\eloquent\MenteeRating;

class MenteeRatingStorage {
    public function rateMentee($sessionId, $menteeId, $mentorId, $rating, $ratingDescription): MenteeRating {
        return MenteeRating::updateOrCreate(
            ['session_id' => $sessionId, 'mentee_id' => $menteeId],
            ['session_id' => $sessionId, 'mentee_id' => $menteeId, 'rated_by_id' => $mentorId, 'rating' => $rating, 'rating_description' => $ratingDescription]
        );
    }

    public function getAverageRatingForMentee($menteeId) {
        $avgRating = MenteeRating::where('mentee_id', $menteeId)->avg('rating')->first();
        return $avgRating;
    }
}
