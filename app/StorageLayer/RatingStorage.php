<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 5/31/17
 * Time: 4:06 PM
 */

namespace App\StorageLayer;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RatingStorage
{
    public function rateMentee($sessionId, $menteeId, $mentorId, $rating)
    {
        $id = DB::table('mentee_rating')->insertGetId([
            'rating' => $rating, 'rating_description' => '', 'mentee_id' => $menteeId, 'session_id' => $sessionId,
            'rated_by_id' => $mentorId, 'created_at' => Carbon::now()
        ]);
        return $id;
    }

    public function rateMentor($sessionId, $mentorId, $menteeId, $rating)
    {
        $id = DB::table('mentor_rating')->insertGetId([
            'rating' => $rating, 'rating_description' => '', 'mentor_id' => $mentorId, 'session_id' => $sessionId,
            'rated_by_id' => $menteeId, 'created_at' => Carbon::now()
        ]);
        return $id;
    }
}
