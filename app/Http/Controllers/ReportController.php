<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\MenteeManager;
use App\BusinessLogicLayer\managers\MentorManager;
use App\BusinessLogicLayer\managers\MentorshipSessionManager;

class ReportController extends Controller
{
    private $menteeManager;
    private $mentorManager;
    private $mentorshipSessionManager;

    public function __construct() {
        $this->menteeManager = new MenteeManager();
        $this->mentorManager = new MentorManager();
        $this->mentorshipSessionManager = new MentorshipSessionManager();
    }

    /**
     * Display all reports.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAllReports()
    {
        $pageTitle = 'Reports';
        $menteesCount = $this->menteeManager->getAllMentees()->count();
        $mentorsCount = $this->mentorManager->getAllMentors()->count();
        $mentorshipSessionsCount = $this->mentorshipSessionManager->getAllMentorshipSessions()->count();
        $activeSessionsCount = $this->mentorshipSessionManager->getAllActiveMentorshipSessions()->count();
        $completedSessionsCount = $this->mentorshipSessionManager->getAllCompletedMentorshipSessions()->count();
        return view('reports.index', compact(
                'pageTitle', 'menteesCount', 'mentorsCount', 'mentorshipSessionsCount', 'activeSessionsCount',
                'completedSessionsCount'
            )
        );
    }
}
