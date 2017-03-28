<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/28/17
 * Time: 4:41 PM
 */

namespace App\ViewComposers;


use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MentorComposer
{
    public function __construct() {

    }

    public function compose(View $view) {
        if(Auth::user()->userHasAccessToCRUDMentorsAndMentees()) {
//            $view('mentors.single-mentor-menu')->with(['actionButtonsNum' => 2, 'matchingMode' => false]);
            $view->with(['actionButtonsNum' => 2, 'matchingMode' => false]);
        } else {
            $view->with(['actionButtonsNum' => 1, 'matchingMode' => false]);
        }
    }
}
