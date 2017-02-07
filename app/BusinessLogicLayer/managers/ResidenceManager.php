<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 7/2/2017
 * Time: 2:43 μμ
 */

namespace App\BusinessLogicLayer\managers;


use App\StorageLayer\ResidenceStorage;

class ResidenceManager {
    private $residencetorage;

    public function __construct() {
        $this->residencetorage = new ResidenceStorage();
    }

    public function getResidence($id) {
        return $this->residencetorage->getResidenceById($id);
    }

    public function getAllResidences() {
        return $this->residencetorage->getAllResidences();
    }
}