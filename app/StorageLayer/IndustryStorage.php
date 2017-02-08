<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 7/2/2017
 * Time: 12:57 μμ
 */

namespace App\StorageLayer;


use App\Models\eloquent\Industry;
use App\Models\eloquent\MentorIndustry;

class IndustryStorage {

    public function getAllIndustries() {
        return Industry::all();
    }

    public function getIndustryById($id) {
        return Industry::find($id);
    }

    public function saveAdditionalSpecialty(MentorIndustry $newMentorSpecialty) {
        $newMentorSpecialty->save();
        return $newMentorSpecialty;
    }

    public function getIndustryForMentor($mentorProfileId, $industryId) {
        return MentorIndustry::where(['mentor_profile_id' => $mentorProfileId, 'industry_id' => $industryId])->firstOrFail();
    }
}