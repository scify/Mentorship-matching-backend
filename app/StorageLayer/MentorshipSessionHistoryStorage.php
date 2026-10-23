<?php

/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/31/17
 * Time: 2:32 PM
 */

namespace App\StorageLayer;

use App\Models\eloquent\MentorshipSessionHistory;

class MentorshipSessionHistoryStorage {
    public function saveMentorshipSessionHistory(MentorshipSessionHistory $mentorshipSessionHistory) {
        return $mentorshipSessionHistory->save();
    }

    public function getMentorshipSessionHistory(int $mentorshipSessionHistoryId): ?MentorshipSessionHistory {
        return MentorshipSessionHistory::find($mentorshipSessionHistoryId);
    }

    public function deleteMentorshipSessionHistory(MentorshipSessionHistory $mentorshipSessionHistory): void {
        $mentorshipSessionHistory->delete();
    }
}
