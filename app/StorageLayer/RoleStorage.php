<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 3/2/2017
 * Time: 3:06 μμ
 */

namespace App\StorageLayer;


use App\Models\eloquent\Role;

class RoleStorage {
    public function getRoleById($id) {
        return Role::findOrFail($id);
    }
}