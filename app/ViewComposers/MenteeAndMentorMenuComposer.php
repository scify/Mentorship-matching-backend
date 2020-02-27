<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/28/17
 * Time: 4:41 PM
 */

namespace App\ViewComposers;


use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class MenteeAndMentorMenuComposer
{
    private $request = null;

    public function __construct(Request $request) {
        $this->request =$request;
    }

    public function compose(View $view) {
        $routeName =  $this->request->has('currentRouteName') ? $this->request->get('currentRouteName') : Route::currentRouteName();

        $routesInMatchingMode = ['showMentorProfilePage', 'showMenteeProfilePage'];
        $matchingMode = false;
        if(in_array($routeName, $routesInMatchingMode)) {
            $matchingMode = true;
        }
        $view->with(['matchingMode' => $matchingMode]);
    }
}
