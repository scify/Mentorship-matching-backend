<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/7/17
 * Time: 4:25 PM
 */

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\MenteeManager;
use App\BusinessLogicLayer\managers\MentorManager;
use App\BusinessLogicLayer\managers\UserManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\OperationResponse;

class SearchController extends Controller
{
    private $userManager;
    private $menteeManager;
    private $mentorManager;

    public function __construct()
    {
        $this->userManager = new UserManager();
        $this->menteeManager = new MenteeManager();
        $this->mentorManager = new MentorManager();
    }

    public function filterResultsByString(Request $request)
    {
        $input = $request->all();
        if(empty($input['search_query'])) {
            $mentors = $this->mentorManager->getAllMentors();
            $mentees = $this->menteeManager->getAllMentees();
            $users = $this->userManager->getAllUsers();
        } else {
            $mentors = $this->mentorManager->filterMentorsByNameAndEmail($input['search_query']);
            $mentees = $this->menteeManager->filterMenteesByNameAndEmail($input['search_query']);
            $users = $this->userManager->filterUsersByNameAndEmail($input['search_query']);
        }
        $loggedInUser = Auth::user();
        if($mentors->count() + $mentees->count() + $users->count() == 0) {
            $errorMessage = "No mentors, mentees or users found";
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        } else {
            return json_encode(new OperationResponse(config('app.OPERATION_SUCCESS'),
                (String)view('common.search.results', compact('mentors', 'mentees', 'users', 'loggedInUser'))));
        }
    }
}
