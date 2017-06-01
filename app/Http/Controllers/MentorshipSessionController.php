<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\MentorshipSessionManager;
use App\BusinessLogicLayer\managers\MentorshipSessionStatusManager;
use App\BusinessLogicLayer\managers\UserManager;
use App\Http\OperationResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class MentorshipSessionController extends Controller
{
    private $mentorshipSessionManager;

    public function __construct() {
        $this->mentorshipSessionManager = new MentorshipSessionManager();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userManager = new UserManager();
        $accountManagers = $userManager->getAccountManagersWithRemainingCapacity();
        $matchers = $userManager->getAllUsersWithMatchingPermissions();
        $mentorshipSessionStatusManager = new MentorshipSessionStatusManager();
        $statuses = $mentorshipSessionStatusManager->getAllMentorshipSessionStatuses();
        $mentorshipSessionViewModels = $this->mentorshipSessionManager->paginateMentorshipSessions($this->mentorshipSessionManager->getAllMentorshipSessionsViewModel())->setPath('#');
        $loggedInUser = Auth::user();
        $pageTitle = 'Sessions';
        $pageSubTitle = 'view all';
        $displayAccountManagerFilter = true;
        $displayMatcherFilter = true;
        return view('mentorship_session.list_all', compact('mentorshipSessionViewModels', 'pageTitle', 'pageSubTitle',
            'loggedInUser', 'statuses', 'accountManagers', 'matchers', 'displayAccountManagerFilter', 'displayMatcherFilter'
        ));
    }

    /**
     * Request contains the mentor and mentee id
     * as well as the account manager id
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'mentor_profile_id' => 'required|numeric',
            'mentee_profile_id' => 'required|numeric',
            'account_manager_id' => 'required|numeric'
        ]);
        $input = $request->all();
        try {
            $this->mentorshipSessionManager->createMentorshipSession($input);
        } catch (\Exception $e) {
            Log::info('Error on session creation: ' . $e->getCode() . "  " . $e->getMessage());
            session()->flash('flash_message_failure', 'An error occurred. Please try again later.');
            return back();
        }
        session()->flash('flash_message_success', 'Mentorship session created');
        return redirect()->route('showMatchesForMatcher');
    }

    public function sendInviteToMentee(Request $request) {
        $this->validate($request, [
            'session_id' => 'required|numeric'
        ]);
        $input = $request->all();
        try {
            $this->mentorshipSessionManager->inviteMentee($input['session_id']);
        }
        catch (\Exception $e) {
            Log::info('Error on mentee invitation sent: ' . $e->getCode() . "  " . $e->getMessage());
            session()->flash('flash_message_failure', 'An error occurred. Please try again later.');
            return back();
        }
        session()->flash('flash_message_success', 'Mentee invited');
        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'account_manager_id' => 'numeric',
            'status_id' => 'required|numeric'
        ]);
        $input = $request->all();
        try {
            $messageToShow = $this->mentorshipSessionManager->editMentorshipSession($input);
        } catch(\Exception $e) {
            Log::info('Error on session update: ' . $e->getCode() . "  " . $e->getMessage());
            session()->flash('flash_message_failure', 'An error occurred. Please try again later.');
            return back();
        }
        session()->flash('flash_message_success', $messageToShow);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'mentorship_session_id' => 'required|numeric'
        ]);
        $input = $request->all();
        try {
            $this->mentorshipSessionManager->deleteMentorshipSession($input);
        } catch(\Exception $e) {
            Log::info('Error on session deletion: ' . $e->getCode() . "  " . $e->getMessage());
            session()->flash('flash_message_failure', 'An error occurred. Please try again later.');
        }
        session()->flash('flash_message_success', 'Mentorship session deleted');
        return back();
    }

    public function showMentorshipSessionsForAccountManager() {
        $loggedInUser = Auth::user();
        $userManager = new UserManager();
        $accountManagers = $userManager->getAccountManagersWithRemainingCapacity();
        $mentorshipSessionStatusManager = new MentorshipSessionStatusManager();
        $statuses = $mentorshipSessionStatusManager->getAllMentorshipSessionStatuses();
        $mentorshipSessionViewModels = $this->mentorshipSessionManager
            ->paginateMentorshipSessions($this->mentorshipSessionManager
            ->getMentorshipSessionViewModelsForAccountManager($loggedInUser->id))->setPath("#");
        $matchers = $userManager->getAllUsersWithMatchingPermissions();
        $pageTitle = 'Sessions';
        $pageSubTitle = 'my mentorship sessions';
        $displayAccountManagerFilter = false;
        $displayMatcherFilter = true;
        $mentorshipSessionPagination = false;
        return view('mentorship_session.list_all', compact('mentorshipSessionViewModels', 'pageTitle', 'pageSubTitle',
            'loggedInUser', 'statuses', 'accountManagers', 'matchers', 'mentorshipSessionPagination', 'displayAccountManagerFilter', 'displayMatcherFilter'
        ));
    }

    public function showMentorshipSessionsForMatcher() {
        $loggedInUser = Auth::user();
        $userManager = new UserManager();
        $accountManagers = $userManager->getAccountManagersWithRemainingCapacity();
        $mentorshipSessionStatusManager = new MentorshipSessionStatusManager();
        $statuses = $mentorshipSessionStatusManager->getAllMentorshipSessionStatuses();
        $mentorshipSessionViewModels = $this->mentorshipSessionManager
            ->paginateMentorshipSessions($this->mentorshipSessionManager
            ->getMentorshipSessionViewModelsForMatcher($loggedInUser->id))->setPath("#");
        $matchers = $userManager->getAllUsersWithMatchingPermissions();
        $pageTitle = 'Sessions';
        $pageSubTitle = 'my matches';
        $displayAccountManagerFilter = true;
        $displayMatcherFilter = false;
        $mentorshipSessionPagination = false;
        return view('mentorship_session.list_all', compact('mentorshipSessionViewModels', 'pageTitle', 'pageSubTitle',
            'loggedInUser', 'statuses', 'accountManagers', 'matchers', 'mentorshipSessionPagination', 'displayAccountManagerFilter', 'displayMatcherFilter'
        ));
    }

    public function getHistoryForMentorshipSession(Request $request) {
        $input = $request->all();
        try {
            $mentorshipSessionViewModel = $this->mentorshipSessionManager->getMentorshipSessionViewModel(
                $this->mentorshipSessionManager->getMentorshipSession($input["mentorship_session_id"])
            );
            $history = $mentorshipSessionViewModel->mentorshipSession->history;
        }  catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getCode() . "  " .  $e->getMessage();
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        }
        if($history->count() === 0) {
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', ['errorMessage' => 'No history found'])));
        } else {
            $loggedInUser = Auth::user();
            return json_encode(new OperationResponse(config('app.OPERATION_SUCCESS'), (String)view('mentorship_session.modals.partials.history_timeline', compact('history', 'loggedInUser'))));
        }
    }

    public function showMentorshipSessionsByCriteria(Request $request) {
        $input = $request->all();
        try {
            $mentorshipSessionsViewModelsData = $this->mentorshipSessionManager->getMentorshipSessionViewModelsByCriteria($input);
            $mentorshipSessionViewModels = $this->mentorshipSessionManager->paginateMentorshipSessions($mentorshipSessionsViewModelsData)->setPath('#');

        }  catch (\Exception $e) {
            Log::info('Error on sessions search: ' . $e->getCode() . "  " .  $e->getMessage());
            $errorMessage = 'An error occurred. Please try again later.';
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        }

        if($mentorshipSessionViewModels->count() == 0) {
            $errorMessage = "No mentorship sessions found";
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        } else {
            $loggedInUser = Auth::user();
            return json_encode(new OperationResponse(config('app.OPERATION_SUCCESS'), (String) view('mentorship_session.list', compact('mentorshipSessionViewModels','loggedInUser'))));
        }
    }

    /**
     * Triggered when an account manager is accepting an invitation to manage a new session
     *
     * @param $mentorshipSessionId int The session's id that will be accepted
     * @param $id int The session's account manager id
     * @param $email string The account manager's email
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function acceptToManageMentorshipSession($mentorshipSessionId, $id, $email) {
        $viewTitle = "Manage Mentorship Session";
        try {
            if($this->mentorshipSessionManager->acceptToManageMentorshipSession($mentorshipSessionId, $id, $email)) {
                return view('common.response-to-email')->with([
                    'message_success' => 'You have successfully accepted to manage the session',
                    'title' => $viewTitle
                ]);
            } else {
                return view('common.response-to-email')->with([
                    'message_failure' => 'You are not permitted to manage this session',
                    'title' => $viewTitle
                ]);
            }
        } catch(\Exception $e) {
            Log::info('Error on session acceptance by manager: ' . $e->getCode() . "  " .  $e->getMessage());
            return view('common.response-to-email')->with([
                'message_failure' => 'An error occurred.',
                'title' => $viewTitle
            ]);
        }
    }

    /**
     * Triggered when an account manager is declining an invitation to manage a new session
     *
     * @param $mentorshipSessionId int The session's id that will be accepted
     * @param $id int The session's account manager id
     * @param $email string The account manager's email
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function declineToManageMentorshipSession($mentorshipSessionId, $id, $email) {
        $viewTitle = "Manage Mentorship Session";
        try {
            if($this->mentorshipSessionManager->declineToManageMentorshipSession($mentorshipSessionId, $id, $email)) {
                return view('common.response-to-email')->with([
                    'message_success' => 'You have declined to manage the session',
                    'title' => $viewTitle
                ]);
            } else {
                return view('common.response-to-email')->with([
                    'message_failure' => 'You are not permitted to manage this session',
                    'title' => $viewTitle
                ]);
            }
        } catch(\Exception $e) {
            Log::info('Error on session decline by manager: ' . $e->getCode() . "  " .  $e->getMessage());
            return view('common.response-to-email')->with([
                'message_failure' => 'An error occurred.',
                'title' => $viewTitle
            ]);
        }
    }

    /**
     * Triggered when a mentor or a mentee accepts a new session invitation
     *
     * @param $mentorshipSessionId string The @see MentorshipSession id
     * @param $role string The role of the person that responded. It could be 'mentor' or 'mentee'
     * @param $id string The id of the person that responded
     * @param $email string The email address of the person responded
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function acceptMentorshipSession($mentorshipSessionId, $role, $id, $email) {
        $lang = Input::has('lang') ? Input::get('lang') : 'en';
        App::setLocale($lang);
        $viewTitle = Lang::get('messages.response_to_session_invitation');
        try {
            if($this->mentorshipSessionManager->acceptMentorshipSession($mentorshipSessionId, $role, $id, $email)) {
                return view('common.response-to-email')->with([
                    'message_success' => Lang::get('messages.invitation_accepted'),
                    'title' => $viewTitle
                ]);
            } else {
                return view('common.response-to-email')->with([
                    'message_failure' => Lang::get('messages.not_permitted_to_respond_invitation'),
                    'title' => $viewTitle
                ]);
            }
        } catch(\Exception $e) {
            Log::info('Error on session acceptance: ' . $e->getCode() . "  " .  $e->getMessage());
            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.error_occurred'),
                'title' => $viewTitle
            ]);
        }
    }

    /**
     * Triggered when a mentor or a mentee declines a new session invitation
     *
     * @param $mentorshipSessionId string The @see MentorshipSession id
     * @param $role string The role of the person that responded. It could be 'mentor' or 'mentee'
     * @param $id string The id of the person that responded
     * @param $email string The email address of the person responded
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function declineMentorshipSession($mentorshipSessionId, $role, $id, $email) {
        $lang = Input::has('lang') ? Input::get('lang') : 'en';
        App::setLocale($lang);
        $viewTitle = Lang::get('messages.response_to_session_invitation');
        try {
            if($this->mentorshipSessionManager->declineMentorshipSession($mentorshipSessionId, $role, $id, $email)) {
                return view('common.response-to-email')->with([
                    'message_success' => Lang::get('messages.invitation_declined'),
                    'title' => $viewTitle
                ]);
            } else {
                return view('common.response-to-email')->with([
                    'message_failure' => Lang::get('messages.not_permitted_to_respond_invitation'),
                    'title' => $viewTitle
                ]);
            }
        } catch(\Exception $e) {
            Log::info('Error on session decline: ' . $e->getCode() . "  " .  $e->getMessage());
            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.error_occurred'),
                'title' => $viewTitle
            ]);
        }
    }
}
