<?php

namespace App\StorageLayer;

use App\Models\eloquent\MentorProfile;

/**
 * Class MentorProfileStorage
 * @package app\StorageLayer
 *
 * Contains the eloquent queries methods for the MentorProfiles.
 */
class MentorStorage {

    public function saveMentor(MentorProfile $mentor) {
        $mentor->save();
        return $mentor;
    }

    public function getAllMentorProfiles() {
        return MentorProfile::all();
    }

    public function getMentorProfileById($id) {
        return MentorProfile::find($id);
    }
}