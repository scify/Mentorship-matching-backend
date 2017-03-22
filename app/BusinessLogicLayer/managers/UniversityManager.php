<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/22/17
 * Time: 12:43 PM
 */

namespace App\BusinessLogicLayer\managers;

use App\StorageLayer\UniversityStorage;

class UniversityManager
{
    private $universityStorage;

    public function __construct() {
        $this->universityStorage = new UniversityStorage();
    }

    public function getAllUniversities() {
        return $this->universityStorage->getAllUniversities();
    }
}
