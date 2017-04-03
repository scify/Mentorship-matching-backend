<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\MentorshipSessionManager;
use App\BusinessLogicLayer\managers\MentorshipSessionStatusManager;
use App\BusinessLogicLayer\managers\UserManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $mentorshipSessionStatusManager = new MentorshipSessionStatusManager();
        $statuses = $mentorshipSessionStatusManager->getAllMentorshipSessionStatuses();
        $mentorshipSessionViewModels = $this->mentorshipSessionManager->getAllMentorshipSessionsViewModel();
        $isCreatingNewSession = false;
        $loggedInUser = Auth::user();
        $pageTitle = 'Sessions';
        $pageSubTitle = 'view all';
        return view('mentorship_session.list_all', compact('mentorshipSessionViewModels', 'pageTitle', 'pageSubTitle',
            'loggedInUser', 'isCreatingNewSession', 'statuses', 'accountManagers'
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
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " . $e->getMessage());
        }
        session()->flash('flash_message_success', 'Mentorship session created');
        return back();
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
            $this->mentorshipSessionManager->editMentorshipSession($input);
        } catch(\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " . $e->getMessage());
        }
        session()->flash('flash_message_success', 'Mentorship session updated');
        return back();
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
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " . $e->getMessage());
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
        $mentorshipSessionViewModels = $this->mentorshipSessionManager->getMentorshipSessionViewModelsForAccountManager($loggedInUser->id);
        $isCreatingNewSession = false;
        $pageTitle = 'Sessions';
        $pageSubTitle = 'my mentorhsip sessions';
        return view('mentorship_session.list_all', compact('mentorshipSessionViewModels', 'pageTitle', 'pageSubTitle',
            'loggedInUser', 'isCreatingNewSession', 'statuses', 'accountManagers'
        ));
    }

    public function showMentorshipSessionsForMatcher() {
        $loggedInUser = Auth::user();
        $userManager = new UserManager();
        $accountManagers = $userManager->getAccountManagersWithRemainingCapacity();
        $mentorshipSessionStatusManager = new MentorshipSessionStatusManager();
        $statuses = $mentorshipSessionStatusManager->getAllMentorshipSessionStatuses();
        $mentorshipSessionViewModels = $this->mentorshipSessionManager->getMentorshipSessionViewModelsForMatcher($loggedInUser->id);
        $isCreatingNewSession = false;
        $pageTitle = 'Sessions';
        $pageSubTitle = 'my matches';
        return view('mentorship_session.list_all', compact('mentorshipSessionViewModels', 'pageTitle', 'pageSubTitle',
            'loggedInUser', 'isCreatingNewSession', 'statuses', 'accountManagers'
        ));
    }
}
