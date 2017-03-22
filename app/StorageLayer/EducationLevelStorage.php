<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/22/17
 * Time: 12:43 PM
 */

namespace App\StorageLayer;

use App\Models\eloquent\EducationLevel;

class EducationLevelStorage
{
    public function getAllEducationLevels() {
        return EducationLevel::all();
    }
}
