<?php

namespace App\StorageLayer;


use App\Models\eloquent\MentorRating;

class MentorRatingStorage {
    public function rateMentor($sessionId, $mentorId, $menteeId, $rating, $ratingDescription): MentorRating {
        return MentorRating::updateOrCreate(
            ['session_id' => $sessionId, 'mentor_id' => $mentorId],
            ['session_id' => $sessionId, 'mentor_id' => $mentorId, 'rated_by_id' => $menteeId, 'rating' => $rating, 'rating_description' => $ratingDescription]
        );
    }

    public function getAverageRatingForMentor($mentorId) {
        $avgRating = MentorRating::where('mentor_id', $mentorId)->avg('rating')->first();
        return $avgRating;
    }
}
