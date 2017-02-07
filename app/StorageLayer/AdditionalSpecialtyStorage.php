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

class AdditionalSpecialtyStorage {

    public function getAllAdditionalSpecialties() {
        return Industry::all();
    }

    public function getAdditionalSpecialtyById($id) {
        return Industry::find($id);
    }

    public function saveAdditionalSpecialty(MentorIndustry $newMentorSpecialty) {
        $newMentorSpecialty->save();
        return $newMentorSpecialty;
    }
}