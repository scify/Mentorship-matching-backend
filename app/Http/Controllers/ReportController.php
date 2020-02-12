<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\MenteeManager;
use App\BusinessLogicLayer\managers\MentorManager;
use App\BusinessLogicLayer\managers\MentorshipSessionManager;
use App\BusinessLogicLayer\managers\UserManager;
use App\StorageLayer\MenteeStorage;
use App\StorageLayer\MentorshipSessionStorage;
use App\StorageLayer\MentorStorage;
use App\Utils\DataToCsvExportManager;
use Carbon\Carbon;


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
        $userManager = new UserManager();
        $adminsCount = $userManager->getAllAdmnins()->count();
        $accountManagersCount = $userManager->getAllAccountManagers()->count();
        $matchersCount = $userManager->getAllMatchers()->count();
        return view('reports.index', compact(
                'pageTitle', 'menteesCount', 'mentorsCount', 'mentorshipSessionsCount', 'activeSessionsCount',
                'completedSessionsCount', 'adminsCount', 'accountManagersCount', 'matchersCount'
            )
        );
    }

    public function exportMentorsToCsv()
    {
        $lang = Request::has('lang') ? Request::get('lang') : 'en';
        $date = str_replace(" ", "_", Carbon::now('Europe/Athens')->toDateTimeString());
        $fileName = "mentors_" . $date . ".csv";
        $dataToCsvExportManager = new DataToCsvExportManager($fileName, MentorStorage::class, ['specialty_name', 'industry_name'], $lang);
        return $dataToCsvExportManager->getExportedData();
    }

    public function exportMenteesToCsv()
    {
        $lang = Request::has('lang') ? Request::get('lang') : 'en';
        $date = str_replace(" ", "_", Carbon::now('Europe/Athens')->toDateTimeString());
        $fileName = "mentees_" . $date . ".csv";
        $dataToCsvExportManager = new DataToCsvExportManager($fileName, MenteeStorage::class, [], $lang);
        return $dataToCsvExportManager->getExportedData();
    }

    public function exportMentorshipSessionsToCsv()
    {
        $lang = Request::has('lang') ? Request::get('lang') : 'en';
        $date = str_replace(" ", "_", Carbon::now('Europe/Athens')->toDateTimeString());
        $fileName = "mentorship_sessions_" . $date . ".csv";
        $dataToCsvExportManager = new DataToCsvExportManager($fileName, MentorshipSessionStorage::class, [], $lang);
        return $dataToCsvExportManager->getExportedData();
    }
}
