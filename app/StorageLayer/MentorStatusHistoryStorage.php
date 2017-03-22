<?php

namespace App\StorageLayer;

class MentorStatusHistoryStorage {

    public function createMentorStatusHistory($mentorStatusHistory) {
        return $mentorStatusHistory->save();
    }

}
