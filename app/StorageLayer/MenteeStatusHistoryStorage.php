<?php

namespace App\StorageLayer;

class MenteeStatusHistoryStorage {

    public function createMenteeStatusHistory($menteeStatusHistory) {
        return $menteeStatusHistory->save();
    }

}
