<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/28/17
 * Time: 4:41 PM
 */

namespace App\ViewComposers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class MenteeAndMentorMenuComposer
{
    public function __construct() {

    }

    public function compose(View $view) {
        $routeName = Route::currentRouteName();
        $routesInMatchingMode = ['showMentorProfilePage', 'showMenteeProfilePage', 'filterMentors', 'filterMentees'];
        $matchingMode = false;
        if(in_array($routeName, $routesInMatchingMode)) {
            $matchingMode = true;
        }
        $view->with(['matchingMode' => $matchingMode]);
    }
}
