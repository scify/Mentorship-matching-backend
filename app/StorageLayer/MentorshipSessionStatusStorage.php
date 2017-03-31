<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/31/17
 * Time: 12:23 PM
 */

namespace App\StorageLayer;


use App\Models\eloquent\MentorshipSessionStatus;

class MentorshipSessionStatusStorage
{
    public function getAllMentorshipSessionStatuses() {
        return MentorshipSessionStatus::all();
    }
}
