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
        return MenteeProfile::orderBy('last_name')->orderBy('first_name')->get();
    }

    public function getMenteeProfileById($id) {
        return MenteeProfile::find($id);
    }

    public function getMenteesThatMatchGivenNameOrEmail($searchQuery) {
        return MenteeProfile::where('first_name', 'like', '%' . $searchQuery . '%')
            ->orWhere('last_name', 'like', '%' . $searchQuery . '%')
            ->orWhere('email', 'like', '%' . $searchQuery . '%')->orderBy('last_name')->orderBy('first_name')->get();
    }

    public function getMenteesFromIdsArray($filteredMenteeIds) {
        return MenteeProfile::whereIn('id', $filteredMenteeIds)->orderBy('last_name')->orderBy('first_name')->get();
    }
}
