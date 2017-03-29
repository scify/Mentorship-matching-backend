<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/29/17
 * Time: 5:50 PM
 */

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\MentorshipSession;
use App\StorageLayer\MentorshipSessionStorage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MentorshipSessionManager
{
    private $mentorshipSessionStorage;

    public function __construct() {
        $this->mentorshipSessionStorage = new MentorshipSessionStorage();
    }

    private function assignInputFieldsToMentorshipSession(MentorshipSession $mentorshipSession, array $input) {
        $mentorshipSession->fill($input);
        return $mentorshipSession;
    }

    public function createMentorshipSession(array $input) {
        $loggedInUser = Auth::user();
        if($loggedInUser != null) {
            $input['matcher_id'] = $loggedInUser->id;
        }
        $mentorshipSession = new MentorshipSession();
        $mentorshipSession = $this->assignInputFieldsToMentorshipSession($mentorshipSession, $input);

        DB::transaction(function() use($mentorshipSession, $input) {
            $this->mentorshipSessionStorage->saveMentorshipSession($mentorshipSession);
        });
    }
}
