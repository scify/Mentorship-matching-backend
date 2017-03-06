<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/6/17
 * Time: 1:52 PM
 */

namespace App\StorageLayer;


use App\Models\eloquent\UserIcon;

class UserIconStorage
{

    public function getUserIcons()
    {
        return UserIcon::all();
    }

    public function getIdFromTitle($title) {
        return (UserIcon::where('title', $title)->first())->id;
    }
}
