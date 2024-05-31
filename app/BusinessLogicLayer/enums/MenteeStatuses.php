<?php

namespace App\BusinessLogicLayer\enums;

abstract class MenteeStatuses {
    public static $statuses = array(
        'available' => 1,
        'matched' => 2,
        'completed' => 3,
        'rejected' => 4,
        'black_listed' => 5,
        'followed_up' => 6,
    );
}