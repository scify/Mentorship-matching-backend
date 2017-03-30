<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/30/17
 * Time: 12:56 PM
 */

namespace App\Models\viewmodels;


use App\BusinessLogicLayer\managers\MenteeManager;
use App\BusinessLogicLayer\managers\MentorManager;
use App\Models\eloquent\MentorshipSession;

class MentorshipSessionViewModel
{
    public $mentorshipSession;

    public $mentorViewModel;

    public $menteeViewModel;

    public $accountManager;

    public $matcher;

    public $status;

    public function __construct($mentorshipSession) {
        $this->mentorshipSession = $mentorshipSession;
        $this->mentorViewModel = (new MentorManager())->getMentorViewModel($mentorshipSession->mentor);
        $this->menteeViewModel = (new MenteeManager())->getMenteeViewModel($mentorshipSession->mentee);
        $this->accountManager = $mentorshipSession->account_manager;
        $this->matcher = $mentorshipSession->matcher;
        $this->status = $mentorshipSession->status;
    }
}
