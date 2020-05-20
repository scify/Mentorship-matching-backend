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
use App\Notifications\MenteeSessionInvitation;
use App\Notifications\MentorSessionInvitation;
use App\Notifications\MentorStatusReactivation;
use App\StorageLayer\MentorshipSessionStorage;
use App\Notifications\MentorSendRating;
use App\Notifications\MenteeSendRating;
use App\StorageLayer\RawQueryStorage;
use App\Utils\MentorshipSessionStatuses;
use App\Utils\RawQueriesResultsModifier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

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
     * Used when a session is cancelled (except from the case that the session is cancelled by the mentee).
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
     * Sets mentor's status id to available.
     * Used when a session is cancelled by mentee.
     *
     * @param $mentorProfileId @see MentorProfile's id
     */
    private function setMentorshipSessionMentorStatusToAvailable($mentorProfileId) {
        $mentorManager = new MentorManager();
        // INFO: mentor status id 1 means available
        $mentorManager->editMentor(array('status_id' => 1), $mentorProfileId);
    }

    /**
     * Sets mentor's status id to not available.
     * Used when a session is cancelled by mentee.
     *
     * @param $mentorProfileId @see MentorProfile's id
     */
    private function setMentorshipSessionMentorStatusToNotAvailable($mentorProfileId) {
        $mentorManager = new MentorManager();
        // INFO: mentor status id 1 means available
        $mentorManager->editMentor(array('status_id' => 2), $mentorProfileId);
    }

    /**
     * Sets mentor's status id to not available.
     * Used when a session is cancelled by mentee.
     *
     * @param $menteeProfileId @see MentorProfile's id
     */
    private function setMentorshipSessionMenteeStatusToAvailable($menteeProfileId) {
        $menteeManager = new MenteeManager();
        // INFO: mentee status id 1 means available
        $menteeManager->editMentee(array('status_id' => 1), $menteeProfileId);
    }

    /**
     * Sets mentee's status id to rejected.
     * Used when a session is cancelled by mentee.
     *
     * @param $menteeProfileId @see MenteeProfile's id
     */
    private function setMentorshipSessionMenteeStatusToRejected($menteeProfileId) {
        $menteeManager = new MenteeManager();
        // INFO: mentee status id 4 means rejected
        $menteeManager->editMentee(array('status_id' => 4), $menteeProfileId);
    }

    /**
     * Sets mentee's status id to completed.
     * Used when a session is completed.
     *
     * @param $menteeProfileId @see MenteeProfile's id
     */
    private function setMentorshipSessionMenteeStatusToCompleted($menteeProfileId) {
        $menteeManager = new MenteeManager();
        // INFO: mentee status id 3 means completed
        $menteeManager->editMentee(array('status_id' => 3), $menteeProfileId);
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
     * @throws Exception
     */
    public function createMentorshipSession(array $input) {
        $loggedInUser = Auth::user();
        if($loggedInUser != null) {
            $input['matcher_id'] = $loggedInUser->id;
        } else {
            throw new Exception("Not logged in user");
        }
        // set the current (most recent) status id for the mentorship session
        // if matcher id same as account manager id, the most recent is "invitation to mentee and mentor sent"
        // otherwise the most recent is "assigned to account manager"
        if($input['account_manager_id'] == $input['matcher_id']) {
            $input['status_id'] = MentorshipSessionStatuses::$statuses['introduction_sent'];
        } else {
            $input['status_id'] = MentorshipSessionStatuses::$statuses['pending'];
        }

        $mentorshipSession = new MentorshipSession();
        $mentorshipSession = $this->assignInputFieldsToMentorshipSession($mentorshipSession, $input);

        DB::transaction(function() use($mentorshipSession, $loggedInUser, $input) {
            $this->mentorshipSessionStorage->saveMentorshipSession($mentorshipSession);
            $this->mentorshipSessionHistoryManager->createMentorshipSessionStatusHistory($mentorshipSession, MentorshipSessionStatuses::$statuses['pending'], $loggedInUser, "");
            $this->setMentorshipSessionMentorAndMenteeStatusesToNotAvailable($input['mentor_profile_id'], $input['mentee_profile_id']);
            // if matcher id same as account manager id, pass both "assigned to acc manager"
            // and "emailed mentee and mentor to confirm availability" statuses
            // and then send first email to mentee
            if($input['account_manager_id'] == $input['matcher_id']) {
                $this->mentorshipSessionHistoryManager->createMentorshipSessionStatusHistory($mentorshipSession, MentorshipSessionStatuses::$statuses['introduction_sent'], $loggedInUser, "");
                $this->inviteMenteeToMentorshipSession($mentorshipSession);
            } else {
                // else email account manager
                $this->inviteAccountManagerToMentorshipSession($mentorshipSession);
            }

        });
    }


    private function inviteAccountManagerToMentorshipSession(MentorshipSession $mentorshipSession) {
        $accountManager = $mentorshipSession->account_manager;
        (new MailManager())->sendEmailToSpecificEmail(
            'emails.session-invitation',
            ['id' => $accountManager->id, 'email' => $accountManager->email, 'mentorshipSessionId' => $mentorshipSession->id],
            'Job Pairs | You have been invited to manage a new mentorship session | Session: ' . $mentorshipSession->id,
            $accountManager->email
        );
    }

    private function inviteMenteeToMentorshipSession(MentorshipSession $mentorshipSession) {
        $mentee = $mentorshipSession->mentee;
        $mentee->notify(new MenteeSessionInvitation($mentorshipSession));
    }

    private function inviteMentorToMentorshipSession(MentorshipSession $mentorshipSession) {
        $mentor = $mentorshipSession->mentor;
        $mentor->notify(new MentorSessionInvitation($mentorshipSession));
    }

    // Add to mentorshipManager (BL)
    public function sendRatingEmails($session){
        $this->sendRatingToMentor($session);
        $this->sendRatingToMentee($session);
    }

    public function sendRatingToMentor($session){
        $mentor = $session->mentor;
        $mentor->notify(new MentorSendRating($session));
    }

    public function sendRatingToMentee($session){
        $mentee = $session->mentee;
        $mentee->notify(new MenteeSendRating($session));
    }

    /**
     * Check if is necessary to send rating to mentor/mentee.
     * Check if $status_id from current session matches "fourth_meeting" from MentorshipSessionStatuses.php
     */
    public function shouldSendRating($mentorshipSession) {
        // Peek in MentorshipSessionStatuses for the lookup table
        $sessionStatusKey = "fourth_meeting";

        if ($mentorshipSession->status_id == MentorshipSessionStatuses::$statuses[$sessionStatusKey]) {
            $this->sendRatingEmails($mentorshipSession);
            return true;
        }
        return false;
    }

    /**
     * Updates an existing mentorship session in DB
     *
     * @param array $input
     * @return string Message to display to user performing session update on view
     */
    public function editMentorshipSession(array $input)
    {
        $loggedInUser = Auth::user();

        $mentorshipSession = $this->mentorshipSessionStorage->findMentorshipSessionById($input['mentorship_session_id']);
        $input['mentor_profile_id'] = $mentorshipSession->mentor_profile_id;
        $input['mentee_profile_id'] = $mentorshipSession->mentee_profile_id;
        $mentorshipSession = $this->assignInputFieldsToMentorshipSession($mentorshipSession, $input);
        $comment = isset($input['comment']) ? $input['comment'] : "";

        $shouldSetSessionStatusToSentEvaluation = $this->shouldSendRating($mentorshipSession);
        $numberOfNecessarySessionUpdates = $shouldSetSessionStatusToSentEvaluation ? 2 : 1;
        $messageToShow = 'Mentorship session updated.';
        for($i = 0; $i < $numberOfNecessarySessionUpdates; $i++) {
            if($i === 1) {
                $mentorshipSession->status_id = MentorshipSessionStatuses::getCompletedSessionStatuses()[0];
                $messageToShow .= ' Emails asking mentor and mentee to rate each other have been sent.';
            }
            DB::transaction(function () use ($mentorshipSession, $loggedInUser, $comment) {
                $this->mentorshipSessionStorage->saveMentorshipSession($mentorshipSession);
                $this->mentorshipSessionHistoryManager->createMentorshipSessionStatusHistory($mentorshipSession, $mentorshipSession->status_id, $loggedInUser, $comment);
            });
        }

        $this->handleMentorshipSessionStatusUpdate($mentorshipSession, $input);

        return $messageToShow;
    }


    private function handleMentorshipSessionStatusUpdate(MentorshipSession $mentorshipSession, array $input) {
        $mentorshipSessionStatuses = new MentorshipSessionStatuses();
        // if status is a completed status, send email to the mentor to ask if should be available for a new session
        if($mentorshipSession->status_id == $mentorshipSessionStatuses::getCompletedSessionStatuses()[0]) {
            $mentor = $mentorshipSession->mentor;
            $mentor->notify(new MentorStatusReactivation($mentor));
        }
        // if status is cancelled, change the mentor and mentee statuses back to available
        // except from the case when the session is cancelled by the mentee
        if(array_search($mentorshipSession->status_id, $mentorshipSessionStatuses::getCancelledSessionStatuses()) !== false) {
            // status 12 is status "Cancelled by mentee"
            if($mentorshipSession->status_id == 12) {
                // set mentor's status back to available
                $this->setMentorshipSessionMentorStatusToAvailable($input['mentor_profile_id']);
                // set mentee's status back to rejected
                $this->setMentorshipSessionMenteeStatusToRejected($input['mentee_profile_id']);
            }
            // status 13 is status "Cancelled by mentor"
            else if($mentorshipSession->status_id == 13) {
                // set mentor's status to not available
                $this->setMentorshipSessionMentorStatusToNotAvailable($input['mentor_profile_id']);
                // set mentee's status back to available
                $this->setMentorshipSessionMenteeStatusToAvailable($input['mentee_profile_id']);
            }
            else {
                $this->setMentorshipSessionMentorAndMenteeStatusesToAvailable($input['mentor_profile_id'], $input['mentee_profile_id']);
            }
        } else {
            if(array_search($mentorshipSession->status_id, $mentorshipSessionStatuses::getCompletedSessionStatuses()) !== false) {
                $this->setMentorshipSessionMenteeStatusToCompleted($input['mentee_profile_id']);
            } else {
                $this->setMentorshipSessionMentorAndMenteeStatusesToNotAvailable($input['mentor_profile_id'], $input['mentee_profile_id']);
            }
        }
        // if status is set to introduction between mentor and mentee sent, send emails to the mentor and the mentee
        if($mentorshipSession->status_id == $mentorshipSessionStatuses::getIntroductionSentSessionStatus()) {
            // send mail to mentee
            $this->inviteMenteeToMentorshipSession($mentorshipSession);
        } elseif ($mentorshipSession->status_id == MentorshipSessionStatuses::$statuses['available_mentee']) {
            $this->inviteMentorToMentorshipSession($mentorshipSession);
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

    public function getMentorshipSessionsCountForMentor($mentorProfileId) {
        return $this->mentorshipSessionStorage->getMentorshipSessionsCountForMentor($mentorProfileId);
    }

    public function getMentorshipSessionsCountForMentee($menteeProfileId) {
        return $this->mentorshipSessionStorage->getMentorshipSessionsCountForMentee($menteeProfileId);
    }

    public function getMentorshipSessionViewModelsForMentor($mentorProfileId) {
        $mentorshipSessions = $this->mentorshipSessionStorage->getMentorshipSessionsForMentor($mentorProfileId);
        return $this->getMentorshipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    public function getMentorshipSessionViewModelsForMentee($menteeProfileId) {
        $mentorshipSessions = $this->mentorshipSessionStorage->getMentorshipSessionsForMentee($menteeProfileId);
        return $this->getMentorshipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    public function getMentorshipSessionViewModelsForAccountManager($accountManagerId) {
        $mentorshipSessions = $this->mentorshipSessionStorage->getMentorshipSessionViewModelsForAccountManager($accountManagerId);
        return $this->getMentorshipSessionsViewModelsFromCollection($mentorshipSessions);
    }

    public function getPendingMentorshipSessionViewModelsForAccountManager($accountManagerId) {
        $mentorshipSessions = $this->mentorshipSessionStorage->
            getMentorshipSessionViewModelsForAccountManagerByStatusId($accountManagerId, MentorshipSessionStatuses::getPendingSessionStatuses());
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

    public function getActiveMentorshipSessionsNumForAccountManager($accountManagerId) {
        return $this->mentorshipSessionStorage->getMentorshipSessionsNumForAccountManagerByStatusId($accountManagerId, MentorshipSessionStatuses::getActiveSessionStatuses());
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
            //mentor and mentee should become available again
            $this->setMentorshipSessionMentorAndMenteeStatusesToAvailable($mentorshipSession->mentor->id, $mentorshipSession->mentee->id);
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
            (!isset($filters['startStatusId'])  || $filters['startStatusId'] === "") &&
            (!isset($filters['endStatusId'])  || $filters['endStatusId'] === "") &&
            (!isset($filters['startedDateRange'])  || $filters['startedDateRange'] === "") &&
            (!isset($filters['completedDateRange'])  || $filters['completedDateRange'] === "") &&
            (!isset($filters['accountManagerId'])  || $filters['accountManagerId'] === "") &&
            (!isset($filters['matcherId'])  || $filters['matcherId'] === "") &&
            (!isset($filters['userRole'])  || $filters['userRole'] === "")) {
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
            (isset($filters['startStatusId']) && $filters['startStatusId'] !== "") ||
            (isset($filters['endStatusId']) && $filters['endStatusId'] !== "") ||
            (isset($filters['startedDateRange']) && $filters['startedDateRange'] !== "") ||
            (isset($filters['accountManagerId']) && $filters['accountManagerId'] !== "") ||
            (isset($filters['matcherId']) && $filters['matcherId'] !== "") ||
            (isset($filters['userRole']) && $filters['userRole'] !== "")) {
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
        if(isset($filters['startStatusId']) && $filters['startStatusId'] != "") {
            if(intval($filters['startStatusId']) == 0) {
                throw new \Exception("Filter value is not valid.");
            }
            if($whereClauseExists) {
                $dbQuery .= "and ";
            }
            $dbQuery .= "ms.status_id >= " . $filters['startStatusId'] . " ";
            $whereClauseExists = true;
        }
        if(isset($filters['endStatusId']) && $filters['endStatusId'] != "") {
            if(intval($filters['endStatusId']) == 0) {
                throw new \Exception("Filter value is not valid.");
            }
            if($whereClauseExists) {
                $dbQuery .= "and ";
            }
            $dbQuery .= "ms.status_id <= " . $filters['endStatusId'] . " ";
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
            $whereClauseExists = true;
        }
        // check user role
        if (isset($filters['userRole']) && $filters['userRole'] != "") {
            if ($whereClauseExists) {
                $dbQuery .= "and ";
            }
            if ($filters['userRole'] == 'account_manager') {
                $dbQuery .= "ms.account_manager_id=" . Auth::id();
            } else {
                $dbQuery .= "ms.matcher_id=" . Auth::id();
            }
            $dbQuery .= " ";
            $whereClauseExists = true;
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
            //mentor and mentee should become available again
            $this->setMentorshipSessionMentorAndMenteeStatusesToAvailable($mentorshipSession->mentor->id, $mentorshipSession->mentee->id);
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
            // this is the case when the mentee is available
            if($mentorshipSession->status_id === MentorshipSessionStatuses::$statuses['introduction_sent'])
                $statusToSet = MentorshipSessionStatuses::$statuses['available_mentee'];

        } else if($role === 'mentor') {
            $invitedPerson = $mentorshipSession->mentor;
            // this is the case when mentor is available
            if($mentorshipSession->status_id === MentorshipSessionStatuses::$statuses['available_mentee'])
                $statusToSet = MentorshipSessionStatuses::$statuses['available_mentor'];
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
            // if session is cancelled by mentee make available only the mentor and set the mentee's status to rejected,
            // else if session is cancelled by mentor make the mentor unavailable
            // else make both available
            if($statusToSet === 12) {
                $this->setMentorshipSessionMentorStatusToAvailable($mentorshipSession->mentor->id);
                $this->setMentorshipSessionMenteeStatusToRejected($mentorshipSession->mentee->id);
            } else if($statusToSet === 13) {
                $this->setMentorshipSessionMentorStatusToNotAvailable($mentorshipSession->mentor->id);
                $this->setMentorshipSessionMenteeStatusToAvailable($mentorshipSession->mentee->id);
            } else { // TODO: this was created when the account manager could decline to manage a match (maybe we do not need it anymore)
                $this->setMentorshipSessionMentorAndMenteeStatusesToAvailable(
                    $mentorshipSession->mentor->id, $mentorshipSession->mentee->id
                );
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns all the currently active sessions
     *
     * @return mixed
     */
    public function getAllActiveMentorshipSessions() {
        return $this->mentorshipSessionStorage->getAllActiveMentorshipSessions();
    }

    /**
     * Returns all the completed sessions
     *
     * @return mixed
     */
    public function getAllCompletedMentorshipSessions() {
        return $this->mentorshipSessionStorage->getAllCompletedMentorshipSessions();
    }

    /**
     * Returns all the currently active sessions
     *
     * @return mixed
     */
    public function getAllActiveMentorshipSessionsNum() {
        return $this->mentorshipSessionStorage->getAllActiveMentorshipSessionsNum();
    }

    /**
     * Returns all the completed sessions
     *
     * @return mixed
     */
    public function getAllCompletedMentorshipSessionsNum() {
        return $this->mentorshipSessionStorage->getAllCompletedMentorshipSessionsNum();
    }

    /**
     * Returns the last session view model
     *
     * @param $id integer The mentor's profile id
     * @return mixed
     */
    public function getCurrentMentorshipSessionViewModelForMentor($id) {
        $dbQuery = "select last_session_id from (select max(id) as last_session_id, mentor_profile_id 
                        from mentorship_session group by mentor_profile_id)
                        as last_session where mentor_profile_id = $id";
        $rawQueryStorage = new RawQueryStorage();
        $results = $rawQueryStorage->performRawQuery($dbQuery);
        if(!empty($results)) {
            $lastSessionId = $results[0]->last_session_id;
            $lastMentorshipSession = $this->mentorshipSessionStorage->findMentorshipSessionById($lastSessionId);
            if($lastMentorshipSession != null)
                return collect([new MentorshipSessionViewModel($lastMentorshipSession)]);
            else
                return null;
        } else {
            return null;
        }
    }

    /**
     * Returns the last session view model
     *
     * @param $id integer The mentee's profile id
     * @return mixed
     */
    public function getCurrentMentorshipSessionViewModelForMentee($id) {
        $dbQuery = "select last_session_id from (select max(id) as last_session_id, mentee_profile_id 
                        from mentorship_session group by mentee_profile_id)
                        as last_session where mentee_profile_id = $id";
        $rawQueryStorage = new RawQueryStorage();
        $results = $rawQueryStorage->performRawQuery($dbQuery);
        if(!empty($results)) {
            $lastSessionId = $results[0]->last_session_id;
            $lastMentorshipSession = $this->mentorshipSessionStorage->findMentorshipSessionById($lastSessionId);
            if($lastMentorshipSession != null)
                return collect([new MentorshipSessionViewModel($lastMentorshipSession)]);
            else
                return null;
        } else {
            return null;
        }
    }

    public function inviteMentee($mentorshipSessionId) {
        $mentorshipSession = $this->getMentorshipSession($mentorshipSessionId);
        $loggedInUser = Auth::user();
        DB::transaction(function() use($mentorshipSession, $loggedInUser) {
            $this->mentorshipSessionHistoryManager->createMentorshipSessionStatusHistory($mentorshipSession, MentorshipSessionStatuses::$statuses['introduction_sent'], $loggedInUser, "Mentee invited from account manager");
            $mentorshipSession->status_id = MentorshipSessionStatuses::$statuses['introduction_sent'];
            $this->mentorshipSessionStorage->saveMentorshipSession($mentorshipSession);
            $this->inviteMenteeToMentorshipSession($mentorshipSession);
        });
    }

    public function getSessionsForFollowUp() {
        return $this->mentorshipSessionStorage->getMentorshipSessionsForFollowUp();
    }

    public function paginateMentorshipSessions($items, $perPage = 10) {
        //Get current page form url e.g. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $itemsCount = $items ? count($items) : 0;
        // FIX for searching with page number, when search has fetched less results
        // (fix: go to the first page!)
        if($currentPage > ceil($itemsCount / $perPage))
            $currentPage = "1";

        if (!empty($items)) {
            //Slice the collection to get the items to display in current page
            $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage);
        } else {
            $currentPageItems = new Collection();
        }

        //Create our paginator and pass it to the view
        return new LengthAwarePaginator($currentPageItems, $itemsCount, $perPage);
    }
}
