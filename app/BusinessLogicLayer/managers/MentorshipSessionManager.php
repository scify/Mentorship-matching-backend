<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/29/17
 * Time: 5:50 PM
 */

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\MentorshipSession;
use App\Models\viewmodels\MentorshipSessionViewModel;
use App\StorageLayer\MentorshipSessionStorage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MentorshipSessionManager
{
    private $mentorshipSessionStorage;

    private $mentorshipSessionHistoryManager;

    public function __construct() {
        $this->mentorshipSessionStorage = new MentorshipSessionStorage();
        $this->mentorshipSessionHistoryManager = new MentorshipSessionHistoryManager();
    }

    /**
     * Returns a mentorship session object filled with input passed as array
     *
     * @param MentorshipSession $mentorshipSession
     * @param array $input
     * @return MentorshipSession
     */
    private function assignInputFieldsToMentorshipSession(MentorshipSession $mentorshipSession, array $input) {
        $mentorshipSession->fill($input);
        return $mentorshipSession;
    }

    /**
     * Creates a new mentorship session in DB
     *
     * @param array $input
     */
    public function createMentorshipSession(array $input) {
        $loggedInUser = Auth::user();
        if($loggedInUser != null) {
            $input['matcher_id'] = $loggedInUser->id;
        }
        $input['status_id'] = 1;
        $mentorshipSession = new MentorshipSession();
        $mentorshipSession = $this->assignInputFieldsToMentorshipSession($mentorshipSession, $input);

        DB::transaction(function() use($mentorshipSession, $loggedInUser) {
            $this->mentorshipSessionStorage->saveMentorshipSession($mentorshipSession);
            $this->mentorshipSessionHistoryManager->createMentorshipSessionStatusHistory($mentorshipSession, $loggedInUser, "");
        });
    }

    /**
     * Updates an existing mentorship session in DB
     *
     * @param array $input
     */
    public function editMentorshipSession(array $input) {
        $loggedInUser = Auth::user();
        $mentorshipSession = $this->mentorshipSessionStorage->findMentorshipSessionById($input['mentorship_session_id']);
        $input['mentor_profile_id'] = $mentorshipSession->mentor_profile_id;
        $input['mentee_profile_id'] = $mentorshipSession->mentee_profile_id;
        $mentorshipSession = $this->assignInputFieldsToMentorshipSession($mentorshipSession, $input);

        DB::transaction(function() use($mentorshipSession, $loggedInUser) {
            $this->mentorshipSessionStorage->saveMentorshipSession($mentorshipSession);
            // todo: add comment
            $this->mentorshipSessionHistoryManager->createMentorshipSessionStatusHistory($mentorshipSession, $loggedInUser, "");
        });
    }

    /**
     * Returns all the mentorship session stored in the DB
     *
     * @return mixed Collection of mentorship sessions or null if none exists
     */
    public function getAllMentorshipSessions() {
        return $this->mentorshipSessionStorage->getAllMentorshipSessions();
    }

    /**
     * Returns a mentorship session view model containing the mentorship session that is passed as a parameter
     *
     * @param MentorshipSession $mentorshipSession
     * @return MentorshipSessionViewModel
     */
    public function getMentorshipSessionViewModel(MentorshipSession $mentorshipSession) {
        $mentorshipSessionViewModel = new MentorshipSessionViewModel($mentorshipSession);
        return $mentorshipSessionViewModel;
    }

    /**
     * Returns a Collection of view models containing all the mentorship sessions stored in the DB
     *
     * @return Collection
     */
    public function getAllMentorshipSessionsViewModel() {
        $mentorshipSessions = $this->getAllMentorshipSessions();
        return $this->getMentorssipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    public function getMentorshipSessionViewModelsForMentor($mentorProfileId) {
        $mentorshipSessions = $this->mentorshipSessionStorage->getMentorshipSessionViewModelsForMentor($mentorProfileId);
        return $this->getMentorssipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    public function getMentorshipSessionViewModelsForMentee($menteeProfileId) {
        $mentorshipSessions = $this->mentorshipSessionStorage->getMentorshipSessionViewModelsForMentee($menteeProfileId);
        return $this->getMentorssipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    public function getMentorshipSessionViewModelsForAccountManager($accountManagerId) {
        $mentorshipSessions = $this->mentorshipSessionStorage->getMentorshipSessionViewModelsForAccountManager($accountManagerId);
        return $this->getMentorssipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    public function getMentorshipSessionViewModelsForMatcher($matcherId) {
        $mentorshipSessions = $this->mentorshipSessionStorage->getMentorshipSessionViewModelsForMatcher($matcherId);
        return $this->getMentorssipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    private function getMentorssipSessionsViewModelsFromCollection(Collection $mentorshipSessions) {
        $mentorshipSessionViewModels = new Collection();
        foreach ($mentorshipSessions as $mentorshipSession) {
            $mentorshipSessionViewModels->add($this->getMentorshipSessionViewModel($mentorshipSession));
        }
        return $mentorshipSessionViewModels;
    }

    /**
     * Delete a mentorship session from the DB
     *
     * @param array $input
     */
    public function deleteMentorshipSession(array $input) {
        DB::transaction(function() use($input) {
            $mentorshipSession = $this->mentorshipSessionStorage->findMentorshipSessionById($input['mentorship_session_id']);
            $this->mentorshipSessionStorage->deleteMentorshipSession($mentorshipSession);
        });
    }
}
