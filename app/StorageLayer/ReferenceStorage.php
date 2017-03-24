<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 24/3/2017
 * Time: 11:16 πμ
 */

namespace App\StorageLayer;


use App\Models\eloquent\Reference;

class ReferenceStorage {

    public function getAllReferences() {
        return Reference::all();
    }

    public function getReferenceById($id) {
        return Reference::find($id);
    }

}