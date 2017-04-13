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
        'introduction_sent' => 2,
        'available_mentee' => 3,
        'available_mentor' => 4,
        'started' => 5,
        'first_meeting' => 6,
        'second_meeting' => 7,
        'third_meeting' => 8,
        'fourth_meeting' => 9,
        'evaluation_sent' => 10,
        'follow_up_sent' => 11,
        'cancelled_mentee' => 12,
        'cancelled_mentor' => 13,
        'cancelled_acc_man' => 14
    );

    public static function getActiveSessionStatuses() {
        $activeSessionStatuses = array(
            self::$statuses['pending'], self::$statuses['available_mentee'], self::$statuses['available_mentor'],
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

    public static function getPendingSessionStatus() {
        return self::$statuses['pending'];
    }

    public static function getCancelledSessionStatuses() {
        $cancelledSessionStatuses = array(
            self::$statuses['cancelled_mentee'], self::$statuses['cancelled_mentor'], self::$statuses['cancelled_acc_man']
        );
        return $cancelledSessionStatuses;
    }

    public static function getIntroductionSentSessionStatus() {
        return self::$statuses['introduction_sent'];
    }
}
