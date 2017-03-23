<?php

namespace App\StorageLayer;

use App\Models\eloquent\MenteeStatus;

class MenteeStatusStorage {

    public function getMenteeStatuses() {
        return MenteeStatus::all();
    }
}
