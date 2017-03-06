<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/6/17
 * Time: 1:50 PM
 */

namespace App\BusinessLogicLayer\managers;


use App\StorageLayer\UserIconStorage;

class UserIconManager
{
    private $userIconStorage;

    public function __construct()
    {
        $this->userIconStorage = new UserIconStorage();
    }

    public function getAllUserIcons()
    {
        return $this->userIconStorage->getUserIcons();
    }

    public function getIconIdFromTitle($title)
    {
        return $this->userIconStorage->getIdFromTitle($title);
    }
}
