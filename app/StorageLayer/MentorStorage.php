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
        return MentorProfile::orderBy('last_name')->orderBy('first_name')->get();
    }

    public function getMentorProfileById($id) {
        return MentorProfile::find($id);
    }

    public function getMentorsByCompanyId($companyId) {
        return MentorProfile::where(['company_id' => $companyId])
            ->orderBy('last_name')->orderBy('first_name')->get();
    }

    public function getMentorsThatMatchGivenNameOrEmail($searchQuery) {
        return MentorProfile::where('first_name', 'like', '%' . $searchQuery . '%')
            ->orWhere('last_name', 'like', '%' . $searchQuery . '%')
            ->orWhere('email', 'like', '%' . $searchQuery . '%')
            ->orderBy('last_name')->orderBy('first_name')->get();
    }

    public function getMentorsFromIdsArray($filteredMentorIds) {
        return MentorProfile::whereIn('id', $filteredMentorIds)
            ->orderBy('last_name')->orderBy('first_name')->get();
    }

    public function deleteMentor($mentor) {
        $mentor->delete();
    }
}
