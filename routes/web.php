<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('mentor/create', 'MentorController@showCreateForm')->name('showCreateMentorForm');
Route::post('mentor/create', 'MentorController@create')->name('createMentor');

Route::get('mentee/create', 'MenteeController@showCreateForm')->name('showCreateMenteeForm');
Route::post('mentee/create', 'MenteeController@create')->name('createMentee');

Route::get('set-status-available/{id}/{email}', 'MentorController@makeMentorAvailableAgain')->name('setMentorStatusAvailable');

Route::get('accept-session-management/{mentorshipSessionId}/{id}/{email}', 'MentorshipSessionController@acceptToManageMentorshipSession')->name('acceptToManageMentorshipSession');

Route::get('decline-session-management/{mentorshipSessionId}/{id}/{email}', 'MentorshipSessionController@declineToManageMentorshipSession')->name('declineToManageMentorshipSession');

Route::get('accept-session/{mentorshipSessionId}/{role}/{id}/{email}', 'MentorshipSessionController@acceptMentorshipSession')->name('acceptMentorshipSession');

Route::get('decline-session/{mentorshipSessionId}/{role}/{id}/{email}', 'MentorshipSessionController@declineMentorshipSession')->name('declineMentorshipSession');

Route::get('rateMentee/{sessionId}/{mentorId}/{menteeId}', 'RatingController@showMenteeRatingForm')->name('showMenteeRatingForm');
Route::post('rateMentee', 'RatingController@rateMentee')->name('rateMentee');
Route::get('rateMentor/{sessionId}/{menteeId}/{mentorId}', 'RatingController@showMentorRatingForm')->name('showMentorRatingForm');
Route::post('rateMentor', 'RatingController@rateMentor')->name('rateMentor');

Route::group([ 'middleware' => 'auth' ], function () {

    Route::get('/', 'UserController@showDashboardForUser')->name('home');
    Route::get('/dashboard', 'UserController@showDashboardForUser')->name('dashboard');

    Route::get('/testemail', 'HomeController@testEmail')->name('testemail');

    Route::get('mentor/{id}/profile', 'MentorController@showProfile')->name('showMentorProfilePage');
    Route::get('mentee/{id}/profile', 'MenteeController@showProfile')->name('showMenteeProfilePage');
    Route::get('mentors/byCriteria', 'MentorController@showMentorsByCriteria')->name('showMentorsByCriteria');
    Route::get('mentees/filter', 'MenteeController@showMenteesByCriteria')->name('filterMentees');
    Route::get('mentors/filter', 'MentorController@showMentorsByCriteria')->name('filterMentors');
    Route::get('mentors/all', 'MentorController@showAllMentors')->name('showAllMentors');
    Route::get('mentees/all', 'MenteeController@showAllMentees')->name('showAllMentees');

    Route::get('user/{id}/profile', 'UserController@showProfile')->name('showUserProfile');
    Route::get('user/{id}/edit', 'UserController@showEditForm')->name('showEditUserForm');
    Route::post('user/{id}/edit', 'UserController@edit')->name('editUser');
    Route::get('user/{id}/editUserCapacity', ['as' => 'editUserCapacity','uses' => 'UserController@editUserCapacity']);

    Route::get('session/fetchHistory', 'MentorshipSessionController@getHistoryForMentorshipSession')->name('fetchSessionHistory');
    Route::get('sessions/byCriteria', 'MentorshipSessionController@showMentorshipSessionsByCriteria')->name('filterMentorshipSessions');

    Route::post('delete-history-item', 'MentorshipSessionHistoryController@deleteSessionHistoryItem')->name('deleteSessionHistoryItem');
});

Route::group(['middleware' => ['auth', 'admin']], function () {
    Route::get('users/all', 'UserController@showAllUsers')->name('showAllUsers');
    Route::get('user/create', 'UserController@showCreateForm')->name('showCreateUserForm');
    Route::post('user/create', 'UserController@create')->name('createUser');
    Route::post('user/delete', 'UserController@delete')->name('deleteUser');
    Route::post('user/activate', 'UserController@activate')->name('activateUser');
    Route::post('user/deactivate', 'UserController@deactivate')->name('deactivateUser');
    Route::get('users/byCriteria', ['as' => 'getUsersByCriteria','uses' => 'UserController@getUsersByCriteria']);

    Route::get('reports/all', 'ReportController@showAllReports')->name('showAllReports');
    Route::get('exports/mentors', 'ReportController@exportMentorsToCsv')->name('exportMentors');

    Route::get('mentor/{id}/edit', 'MentorController@showEditForm')->name('showEditMentorForm');
    Route::post('mentor/{id}/edit', 'MentorController@edit')->name('editMentor');
    Route::post('mentor/delete', 'MentorController@delete')->name('deleteMentor');


    Route::get('mentee/{id}/edit', 'MenteeController@showEditForm')->name('showEditMenteeForm');
    Route::post('mentee/{id}/edit', 'MenteeController@edit')->name('editMentee');
    Route::post('mentee/delete', 'MenteeController@delete')->name('deleteMentee');

    Route::get('companies/all', 'CompanyController@showAllCompanies')->name('showAllCompanies');
    Route::get('company/create', 'CompanyController@showCreateForm')->name('showCreateCompanyForm');
    Route::post('company/create', 'CompanyController@create')->name('createCompany');
    Route::get('company/{id}/edit', 'CompanyController@showEditForm')->name('showEditCompanyForm');
    Route::post('company/{id}/edit', 'CompanyController@edit')->name('editCompany');
    Route::post('company/delete', 'CompanyController@delete')->name('deleteCompany');

    Route::get('search', 'SearchController@filterResultsByString')->name('search');

    Route::post('session/delete', 'MentorshipSessionController@destroy')->name('deleteMentorshipSession');

    Route::get('upload-mentors', 'ExcelUploadController@mentorsUpload')->name('mentorsUpload');
    Route::get('upload-mentees', 'ExcelUploadController@menteesUpload')->name('menteesUpload');
});

Route::group(['middleware' => ['auth', 'status-changer']], function () {
    Route::post('mentor/changeStatus', 'MentorController@changeMentorAvailabilityStatus')->name('changeMentorStatus');
    Route::post('mentee/changeStatus', 'MenteeController@changeMenteeAvailabilityStatus')->name('changeMenteeStatus');

});

Route::group(['middleware' => ['auth', 'can-create-mentorship-session']], function () {
    Route::post('session/matchMentorWithMentee', 'MentorshipSessionController@create')->name('matchMentorWithMentee');
    Route::get('sessions/myMatches', ['as' => 'showMatchesForMatcher','uses' => 'MentorshipSessionController@showMentorshipSessionsForMatcher']);
});

Route::group(['middleware' => ['auth', 'admin'], ['auth', 'status-changer'], ['auth', 'can-create-mentorship-session']], function () {
    Route::get('sessions/all', 'MentorshipSessionController@index')->name('showAllMentorshipSessions');
    Route::post('session/update', 'MentorshipSessionController@update')->name('updateMentorshipSession');
});

Route::group(['middleware' => ['auth', 'account-manager']], function () {
    Route::get('session/sendInviteToMentee', 'MentorshipSessionController@sendInviteToMentee')->name('sendInviteToMentee');
    Route::post('session/update', 'MentorshipSessionController@update')->name('updateMentorshipSession');
    Route::get('sessions/mySessions', 'MentorshipSessionController@showMentorshipSessionsForAccountManager')->name('showMentorshipSessionsForAccountManager');
});
