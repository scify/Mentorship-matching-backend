<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/22/17
 * Time: 12:43 PM
 */

namespace App\BusinessLogicLayer\managers;

use App\StorageLayer\EducationLevelStorage;

class EducationLevelManager
{
    private $educationLevelStorage;

    public function __construct() {
        $this->educationLevelStorage = new EducationLevelStorage();
    }

    public function getAllEducationLevels() {
        return $this->educationLevelStorage->getAllEducationLevels();
    }
}
