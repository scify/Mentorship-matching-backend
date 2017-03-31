<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/31/17
 * Time: 6:08 PM
 */

namespace App\Utils;


class MentorshipSessionStatuses
{
    private static $statuses = array(
        'pending' => 1,
        'available_mentee' => 2,
        'available_mentor' => 3,
        'started' => 4,
        'first_meeting' => 5,
        'second_meeting' => 6,
        'third_meeting' => 7,
        'fourth_meeting' => 8,
        'evaluation_sent' => 9,
        'follow_up_sent' => 10,
        'cancelled' => 11
    );

    public static function getActiveSessionStatuses() {
        $activeSessionStatuses = array(
            self::$statuses['started'], self::$statuses['first_meeting'], self::$statuses['second_meeting'],
            self::$statuses['third_meeting'], self::$statuses['fourth_meeting']
        );
        return $activeSessionStatuses;
    }

    public static function getCompletedSessionStatuses() {
        $completedSessionStatuses =  array(
            self::$statuses['evaluation_sent'], self::$statuses['follow_up_sent']
        );
        return $completedSessionStatuses;
    }
}
