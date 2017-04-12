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
use App\StorageLayer\RawQueryStorage;
use App\Utils\MentorshipSessionStatuses;
use App\Utils\RawQueriesResultsModifier;
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
     * Sets mentor's and mentee's status id to not available and matched correspondingly.
     * Used when a new session is created.
     *
     * @param $mentorProfileId @see MentorProfile's id
     * @param $menteeProfileId @see MenteeProfile's id
     */
    private function setMentorshipSessionMentorAndMenteeStatusesToNotAvailable($mentorProfileId, $menteeProfileId) {
        $mentorManager = new MentorManager();
        $menteeManager = new MenteeManager();
        DB::transaction(function() use($mentorManager, $mentorProfileId, $menteeManager, $menteeProfileId) {
            // INFO: mentor status id 2 means NOT available for mentorship
            $mentorManager->editMentor(array('status_id' => 2), $mentorProfileId);
            // INFO: mentee status id 2 means matched
            $menteeManager->editMentee(array('status_id' => 2), $menteeProfileId);
        });
    }

    /**
     * Sets mentor's and mentee's status id to available.
     * Used when a session is completed or cancelled.
     *
     * @param $mentorProfileId @see MentorProfile's id
     * @param $menteeProfileId @see MenteeProfile's id
     */
    private function setMentorshipSessionMentorAndMenteeStatusesToAvailable($mentorProfileId, $menteeProfileId) {
        $mentorManager = new MentorManager();
        $menteeManager = new MenteeManager();
        DB::transaction(function() use($mentorManager, $mentorProfileId, $menteeManager, $menteeProfileId) {
            // INFO: mentor status id 1 means available for mentorship
            $mentorManager->editMentor(array('status_id' => 1), $mentorProfileId);
            // INFO: mentee status id 1 means available
            $menteeManager->editMentee(array('status_id' => 1), $menteeProfileId);
        });
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
        $this->setMentorshipSessionMentorAndMenteeStatusesToNotAvailable($input['mentor_profile_id'], $input['mentee_profile_id']);
        $mentorshipSession = new MentorshipSession();
        $mentorshipSession = $this->assignInputFieldsToMentorshipSession($mentorshipSession, $input);

        DB::transaction(function() use($mentorshipSession, $loggedInUser) {
            $this->mentorshipSessionStorage->saveMentorshipSession($mentorshipSession);
            $this->mentorshipSessionHistoryManager->createMentorshipSessionStatusHistory($mentorshipSession, $loggedInUser, "");
        });

        // emails account manager
        $accountManager = $mentorshipSession->account_manager;
        (new MailManager())->sendEmailToSpecificEmail(
            'emails.session-invitation',
            ['id' => $accountManager->id, 'email' => $accountManager->email, 'mentorshipSessionId' => $mentorshipSession->id],
            'Job Pairs | You have been invited to manage a new mentorship session',
            $accountManager->email
        );
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
        $comment = isset($input['comment'])? $input['comment'] : "";

        DB::transaction(function() use($mentorshipSession, $loggedInUser, $comment) {
            $this->mentorshipSessionStorage->saveMentorshipSession($mentorshipSession);
            $this->mentorshipSessionHistoryManager->createMentorshipSessionStatusHistory($mentorshipSession, $loggedInUser, $comment);
        });

        // if status is a completed status, send email to the mentor to ask if should be available for a new session
        $mentorshipSessionStatuses = new MentorshipSessionStatuses();
        if($mentorshipSession->status_id == $mentorshipSessionStatuses::getCompletedSessionStatuses()[0]) {
            $mentor = (new MentorManager())->getMentor($mentorshipSession->mentor_profile_id);
            (new MailManager())->sendEmailToSpecificEmail('emails.reactivate-mentor',
                ['id' => $mentor->id, 'email' => $mentor->email], 'Job Pairs | Mentorship session completed',
                $mentor->email
            );
        }

        // if status is completed or cancelled, change the mentor and mentee statuses back to available
        if(array_search($mentorshipSession->status_id, array_merge(
            $mentorshipSessionStatuses::getCompletedSessionStatuses(), $mentorshipSessionStatuses::getCancelledSessionStatuses()
                )) !== false) {
            $this->setMentorshipSessionMentorAndMenteeStatusesToAvailable($input['mentor_profile_id'], $input['mentee_profile_id']);
        }

        // if status is set to introduction between mentor and mentee sent, send emails to the mentor and the mentee
        if($mentorshipSession->status_id === $mentorshipSessionStatuses::getIntroductionSentSessionStatus()) {
            $mentor = $mentorshipSession->mentor;
            $mentee = $mentorshipSession->mentee;
            $mailManager = new MailManager();
            // send mail to mentor
            $mailManager->sendEmailToSpecificEmail('emails.invite-mentor',
                ['mentor' => $mentor, 'mentee' => $mentee, 'mentorshipSessionId' => $mentorshipSession->id],
                'Job Pairs | You have been matched with a mentee', $mentor->email);
            // send mail to mentee
            $mailManager->sendEmailToSpecificEmail('emails.invite-mentee',
                ['mentor' => $mentor, 'mentee' => $mentee, 'mentorshipSessionId' => $mentorshipSession->id],
                'Job Pairs | You have been matched with a mentor', $mentee->email);
        }
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
     * Returns a @see MentorshipSession with corresponding id
     *
     * @param $id
     * @return mixed
     */
    public function getMentorshipSession($id) {
        return $this->mentorshipSessionStorage->findMentorshipSessionById($id);
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
        return $this->getMentorshipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    public function getMentorshipSessionViewModelsForMentor($mentorProfileId) {
        $mentorshipSessions = $this->mentorshipSessionStorage->getMentorshipSessionViewModelsForMentor($mentorProfileId);
        return $this->getMentorshipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    public function getMentorshipSessionViewModelsForMentee($menteeProfileId) {
        $mentorshipSessions = $this->mentorshipSessionStorage->getMentorshipSessionViewModelsForMentee($menteeProfileId);
        return $this->getMentorshipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    public function getMentorshipSessionViewModelsForAccountManager($accountManagerId) {
        $mentorshipSessions = $this->mentorshipSessionStorage->getMentorshipSessionViewModelsForAccountManager($accountManagerId);
        return $this->getMentorshipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    public function getPendingMentorshipSessionViewModelsForAccountManager($accountManagerId) {
        $mentorshipSessions = $this->mentorshipSessionStorage->
            getMentorshipSessionViewModelsForAccountManagerByStatusId($accountManagerId, MentorshipSessionStatuses::getPendingSessionStatus());
        return $this->getMentorshipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    public function getMentorshipSessionViewModelsForMatcher($matcherId) {
        $mentorshipSessions = $this->mentorshipSessionStorage->getMentorshipSessionViewModelsForMatcher($matcherId);
        return $this->getMentorshipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    private function getMentorshipSessionsViewModelsFromCollection(Collection $mentorshipSessions) {
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

    /**
     * Filters @see MentorshipSession and returns a collection of the filtered sessions view models
     *
     * @param array $filters
     * @return Collection
     */
    public function getMentorshipSessionViewModelsByCriteria(array $filters) {
        $mentorshipSessions = $this->getMentorshipSessionsByCriteria($filters);
        $mentorshipSessionViewModels = $this->getMentorshipSessionsViewModelsFromCollection($mentorshipSessions);
        return $mentorshipSessionViewModels;
    }

    /**
     * Creates the DB query necessary to filter sessions and returns its results
     *
     * @param array $filters
     * @return mixed
     * @throws \Exception
     */
    private function getMentorshipSessionsByCriteria(array $filters) {
        if((!isset($filters['mentorName'])  || $filters['mentorName'] === "") &&
            (!isset($filters['menteeName'])  || $filters['menteeName'] === "") &&
            (!isset($filters['statusId'])  || $filters['statusId'] === "") &&
            (!isset($filters['startedDateRange'])  || $filters['startedDateRange'] === "") &&
            (!isset($filters['completedDateRange'])  || $filters['completedDateRange'] === "") &&
            (!isset($filters['accountManagerId'])  || $filters['accountManagerId'] === "") &&
            (!isset($filters['matcherId'])  || $filters['matcherId'] === "")) {
            return $this->mentorshipSessionStorage->getAllMentorshipSessions();
        }
        $whereClauseExists = false;
        $dbQuery = "select distinct ms.id from mentorship_session as ms left outer join mentor_profile as 
          mentor on mentor.id = ms.mentor_profile_id left outer join mentee_profile as mentee on 
          mentee.id = ms.mentee_profile_id ";
        if(isset($filters['completedDateRange']) && $filters['completedDateRange'] != "") {
            $dateRange = explode(" - ", $filters['completedDateRange']);
            $dateArray = explode("/", $dateRange[0]);
            $start = $dateArray[2] . "-" . $dateArray[1] . "-" . $dateArray[0];
            $dateArray = explode("/", $dateRange[1]);
            $end = $dateArray[2] . "-" . $dateArray[1] . "-" . $dateArray[0];
            $mentorshipSessionStatuses = new MentorshipSessionStatuses();
            $dbQuery .= "inner join  
            (select msh.mentorship_session_id ,
                    msh.status_id, msh.created_at as LastSessionStatus from 
                mentorship_session_history msh inner join
            (
                select mentorship_session_id, 
                        max(id) as last_mentorship_session_history_id
                    from mentorship_session_history as msh
                    group by mentorship_session_id
               ) LastSessionHistoryRecord on LastSessionHistoryRecord.last_mentorship_session_history_id = msh.id
                where msh.status_id  in (" . implode(",", $mentorshipSessionStatuses::getCompletedSessionStatuses()) . ") 
            and (msh.created_at >= date(\"" . $start . "\") and msh.created_at <= date(\"" . $end . "\"))
            ) as completed_sessions on ms.id = completed_sessions.mentorship_session_id ";
        }
        if((isset($filters['mentorName']) && $filters['mentorName'] !== "") ||
            (isset($filters['menteeName']) && $filters['menteeName'] !== "") ||
            (isset($filters['statusId']) && $filters['statusId'] !== "") ||
            (isset($filters['startedDateRange']) && $filters['startedDateRange'] !== "") ||
            (isset($filters['accountManagerId']) && $filters['accountManagerId'] !== "") ||
            (isset($filters['matcherId']) && $filters['matcherId'] !== "")) {
            $dbQuery .= "where ";
        }
        if(isset($filters['mentorName']) && $filters['mentorName'] != "") {
            $dbQuery .= "(mentor.first_name like '%" . $filters['mentorName'] . "%' or mentor.last_name like '%" . $filters['mentorName'] . "%') ";
            $whereClauseExists = true;
        }
        if(isset($filters['menteeName']) && $filters['menteeName'] != "") {
            if($whereClauseExists) {
                $dbQuery .= "and ";
            }
            $dbQuery .= "(mentee.first_name like '%" . $filters['menteeName'] . "%' or mentee.last_name like '%" . $filters['menteeName'] . "%') ";
            $whereClauseExists = true;
        }
        if(isset($filters['statusId']) && $filters['statusId'] != "") {
            if(intval($filters['statusId']) == 0) {
                throw new \Exception("Filter value is not valid.");
            }
            if($whereClauseExists) {
                $dbQuery .= "and ";
            }
            $dbQuery .= "ms.status_id = " . $filters['statusId'] . " ";
            $whereClauseExists = true;
        }
        if(isset($filters['startedDateRange']) && $filters['startedDateRange'] != "") {
            $dateRange = explode(" - ", $filters['startedDateRange']);
            $dateArray = explode("/", $dateRange[0]);
            $start = $dateArray[2] . "-" . $dateArray[1] . "-" . $dateArray[0];
            $dateArray = explode("/", $dateRange[1]);
            $end = $dateArray[2] . "-" . $dateArray[1] . "-" . $dateArray[0];
            if($whereClauseExists) {
                $dbQuery .= "and ";
            }
            $dbQuery .= "(ms.created_at >= date('" . $start. "') and ms.created_at <= date('" . $end . "')) ";
            $whereClauseExists = true;
        }
        if(isset($filters['accountManagerId']) && $filters['accountManagerId'] != "") {
            if(intval($filters['accountManagerId']) == 0) {
                throw new \Exception("Filter value is not valid.");
            }
            if($whereClauseExists) {
                $dbQuery .= "and ";
            }
            $dbQuery .= "ms.account_manager_id = " . $filters['accountManagerId'] . " ";
            $whereClauseExists = true;
        }
        if(isset($filters['matcherId']) && $filters['matcherId'] != "") {
            if(intval($filters['matcherId']) == 0) {
                throw new \Exception("Filter value is not valid.");
            }
            if($whereClauseExists) {
                $dbQuery .= "and ";
            }
            $dbQuery .= "ms.matcher_id = " . $filters['matcherId'] . " ";
        }
        $filteredMentorshipSessionsIds = RawQueriesResultsModifier::transformRawQueryStorageResultsToOneDimensionalArray(
            (new RawQueryStorage())->performRawQuery($dbQuery)
        );
        return $this->mentorshipSessionStorage->getMentorshipSessionsFromIdsArray($filteredMentorshipSessionsIds);
    }

    /**
     * An account manager is accepting an invitation to manage a new session
     *
     * @param $mentorshipSessionId int The session's id that will be accepted
     * @param $id int The session's account manager id
     * @param $email string The account manager's email
     * @return bool Whether succeeded or not
     */
    public function acceptToManageMentorshipSession($mentorshipSessionId, $id, $email) {
        $mentorshipSession = $this->getMentorshipSession($mentorshipSessionId);
        $accountManager = $mentorshipSession->account_manager;
        if($accountManager->id == $id && $accountManager->email == $email && $mentorshipSession->status_id == 1) {
            $this->editMentorshipSession([
                'status_id' => 2, 'mentorship_session_id' => $mentorshipSessionId
            ]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * An account manager is declining an invitation to manage a new session
     *
     * @param $mentorshipSessionId int The session's id that will be accepted
     * @param $id int The session's account manager id
     * @param $email string The account manager's email
     * @return bool Whether succeeded or not
     */
    public function declineToManageMentorshipSession($mentorshipSessionId, $id, $email) {
        $mentorshipSession = $this->getMentorshipSession($mentorshipSessionId);
        $accountManager = $mentorshipSession->account_manager;
        if($accountManager->id == $id && $accountManager->email == $email && $mentorshipSession->status_id == 1) {
            $this->editMentorshipSession([
                'status_id' => 14, 'mentorship_session_id' => $mentorshipSessionId
            ]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * A mentor or a mentee accepts a new session invitation
     *
     * @param $mentorshipSessionId string The @see MentorshipSession id
     * @param $role string The role of the person that responded. It could be 'mentor' or 'mentee'
     * @param $id string The id of the person that responded
     * @param $email string The email address of the person responded
     * @return bool Whether succeeded or not
     */
    public function acceptMentorshipSession($mentorshipSessionId, $role, $id, $email) {
        $statusToSet = -1;
        $invitedPerson = null;
        $mentorshipSession = $this->getMentorshipSession($mentorshipSessionId);
        if($role === 'mentee') {
            $invitedPerson = $mentorshipSession->mentee;
            // if mentor hasn't yet responded set to mentee is available, else set to started
            if($mentorshipSession->status_id === 2)
                $statusToSet = 3;
            else if($mentorshipSession->status_id === 4)
                $statusToSet = 5;
        } else if($role === 'mentor') {
            $invitedPerson = $mentorshipSession->mentor;
            // if mentee hasn't yet responded set to mentor is available, else set to started
            if($mentorshipSession->status_id === 2)
                $statusToSet = 4;
            else if($mentorshipSession->status_id === 3)
                $statusToSet = 5;
        }
        if($statusToSet !== -1 && $invitedPerson->id == $id && $invitedPerson->email === $email) {
            $this->editMentorshipSession([
                'status_id' => $statusToSet, 'mentorship_session_id' => $mentorshipSessionId
            ]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * A mentor or a mentee declines a new session invitation
     *
     * @param $mentorshipSessionId string The @see MentorshipSession id
     * @param $role string The role of the person that responded. It could be 'mentor' or 'mentee'
     * @param $id string The id of the person that responded
     * @param $email string The email address of the person responded
     * @return bool Whether succeeded or not
     */
    public function declineMentorshipSession($mentorshipSessionId, $role, $id, $email) {
        $statusToSet = -1;
        $invitedPerson = null;
        $mentorshipSession = $this->getMentorshipSession($mentorshipSessionId);
        if($role === 'mentee') {
            $invitedPerson = $mentorshipSession->mentee;
            // set to cancelled by mentee if mentor has accepted or not responded yet
            if(in_array($mentorshipSession->status_id, [2, 4]) !== false)
                $statusToSet = 12;
        } else if($role === 'mentor') {
            $invitedPerson = $mentorshipSession->mentor;
            // set to cancelled by mentor if mentee has accepted or not responded yet
            if(in_array($mentorshipSession->status_id, [2, 3]) !== false)
                $statusToSet = 13;
        }
        if($statusToSet !== -1 && $invitedPerson->id == $id && $invitedPerson->email === $email) {
            $this->editMentorshipSession([
                'status_id' => $statusToSet, 'mentorship_session_id' => $mentorshipSessionId
            ]);
            return true;
        } else {
            return false;
        }
    }

}
