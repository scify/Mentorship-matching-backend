<?php

namespace App\StorageLayer;

use App\Models\eloquent\MenteeProfile;

/**
 * Class MenteeStorage
 * @package App\StorageLayer
 *
 * Contains the eloquent queries methods for the MenteeProfiles.
 */
class MenteeStorage {

    public function saveMentee(MenteeProfile $mentee) {
        $mentee->save();
        return $mentee;
    }

    public function getAllMenteeProfiles() {
        return MenteeProfile::all();
    }

    public function getMenteeProfileById($id) {
        return MenteeProfile::find($id);
    }
}