<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 4/13/17
 * Time: 4:51 PM
 */

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\MentorshipSessionHistoryManager;
use App\Http\OperationResponse;
use Illuminate\Http\Request;

class MentorshipSessionHistoryController extends Controller
{
    private $mentorshipSessionHistoryManager;

    public function __construct() {
        $this->mentorshipSessionHistoryManager = new MentorshipSessionHistoryManager();
    }

    public function deleteSessionHistoryItem(Request $request) {
        $input = $request->all();
        try {
            $this->mentorshipSessionHistoryManager->deleteMentorshipSessionStatusHistory($input);
            return json_encode(new OperationResponse(config('app.OPERATION_SUCCESS'), "The session status history item was successfully deleted"));
        } catch(\Exception $e) {
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), "A session status history item couldn't be deleted"));
        }
    }
}
