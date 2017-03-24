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
    private $residenceStorage;

    public function __construct() {
        $this->residenceStorage = new ResidenceStorage();
    }

    public function getResidence($id) {
        return $this->residenceStorage->getResidenceById($id);
    }

    public function getAllResidences() {
        return $this->residenceStorage->getAllResidences();
    }
}