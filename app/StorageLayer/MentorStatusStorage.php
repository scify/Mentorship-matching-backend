<?php

namespace App\StorageLayer;


use App\Models\eloquent\MentorStatus;

class MentorStatusStorage {

    public function getMentorStatuses() {
        return MentorStatus::all();
    }

    public function getMentorStatusesWhereIn(array $mentorStatusIds) {
        return MentorStatus::whereIn('id', $mentorStatusIds)->get();
    }

}