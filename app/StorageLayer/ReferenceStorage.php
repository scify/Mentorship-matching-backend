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
        $ids = [6,5,3,1,2,8,4,7];
        $sorted = Reference::all()->sortBy(function($model) use ($ids) {
            return array_search($model->id, $ids);
        });
        return $sorted ;
    }

    public function getReferenceById($id) {
        return Reference::find($id);
    }

}