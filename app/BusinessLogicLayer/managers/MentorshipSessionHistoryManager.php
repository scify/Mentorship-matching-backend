<?php

/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/31/17
 * Time: 2:30 PM
 */

namespace App\BusinessLogicLayer\managers;

use App\Models\eloquent\MentorshipSession;
use App\Models\eloquent\MentorshipSessionHistory;
use App\Models\eloquent\User;
use App\StorageLayer\MentorshipSessionHistoryStorage;
use Exception;

class MentorshipSessionHistoryManager {
    private $mentorshipSessionHistoryStorage;

    public function __construct() {
        $this->mentorshipSessionHistoryStorage = new MentorshipSessionHistoryStorage;
    }

    public function createMentorshipSessionStatusHistory(MentorshipSession $mentorshipSession, $statusId, ?User $loggedInUser, $comment) {
        $mentorshipSessionHistory = new MentorshipSessionHistory;
        $mentorshipSessionHistory->mentorship_session_id = $mentorshipSession->id;
        $mentorshipSessionHistory->user_id = $loggedInUser != null ? $loggedInUser->id : null;
        $mentorshipSessionHistory->status_id = $statusId;
        $mentorshipSessionHistory->comment = $comment;
        $this->mentorshipSessionHistoryStorage->saveMentorshipSessionHistory($mentorshipSessionHistory);
    }

    /**
     * The author of the history row or an admin may delete it, as the
     * session modal shows the delete button.
     *
     * @throws Exception when the row does not exist or the user may not delete it
     */
    public function deleteMentorshipSessionStatusHistory(int $historyId, User $user): void {
        $history = $this->mentorshipSessionHistoryStorage->getMentorshipSessionHistory($historyId);
        if ($history === null) {
            throw new Exception('Session history item not found');
        }
        if ((int) $history->user_id !== $user->id && !(new UserAccessManager)->userIsAdmin($user)) {
            throw new Exception('Only the author or an admin may delete a session history item');
        }
        $this->mentorshipSessionHistoryStorage->deleteMentorshipSessionHistory($history);
    }
}
