<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 7/2/2017
 * Time: 12:57 μμ
 */

namespace App\StorageLayer;


use App\Models\eloquent\Residence;

class ResidenceStorage {

    public function getAllResidences() {
        return Residence::all();
    }

    public function getResidenceById($id) {
        return Residence::find($id);
    }
}