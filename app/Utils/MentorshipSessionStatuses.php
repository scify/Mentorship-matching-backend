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
    public static $statuses = array(
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

    public static function getActiveSessionStatuses(): array {
        return array(
            self::$statuses['pending'], self::$statuses['introduction_sent'], self::$statuses['available_mentee'],
            self::$statuses['available_mentor'], self::$statuses['started'], self::$statuses['first_meeting'],
            self::$statuses['second_meeting'], self::$statuses['third_meeting'], self::$statuses['fourth_meeting']
        );
    }

    public static function getCompletedSessionStatuses(): array {
        return array(
            self::$statuses['evaluation_sent'], self::$statuses['follow_up_sent']
        );
    }

    public static function getPendingSessionStatuses(): array {
        return array(
            self::$statuses['pending']
        );
    }

    public static function getCancelledSessionStatuses(): array {
        return array(
            self::$statuses['cancelled_mentee'], self::$statuses['cancelled_mentor'], self::$statuses['cancelled_acc_man']
        );
    }

    public static function getIntroductionSentSessionStatus(): int {
        return self::$statuses['introduction_sent'];
    }
}
