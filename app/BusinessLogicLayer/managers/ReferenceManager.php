<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 24/3/2017
 * Time: 11:15 πμ
 */

namespace App\BusinessLogicLayer\managers;


use App\StorageLayer\ReferenceStorage;

class ReferenceManager {

    private $referenceStorage;

    public function __construct() {
        $this->referenceStorage = new ReferenceStorage();
    }

    public function getReference($id) {
        return $this->referenceStorage->getReferenceById($id);
    }

    public function getAllReferences() {
        return $this->referenceStorage->getAllReferences();
    }

}