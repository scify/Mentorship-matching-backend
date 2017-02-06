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


Route::get('/', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('contact', 'HomeController@showContactForm')->name('showContactForm');
Route::post('contact', 'HomeController@sendContactEmail')->name('sendContactEmail');

Route::group([ 'middleware' => 'auth' ], function () {
    //TODO: wrap in middleware for admin

});

Route::group(['middleware' => ['auth', 'admin']], function () {
    Route::get('users/all', 'UserController@showAllUsers')->name('showAllUsers');
    Route::get('user/create', 'UserController@showCreateForm')->name('showCreateUserForm');
    Route::post('user/create', 'UserController@create')->name('createUser');
    Route::get('user/{id}/edit', 'UserController@showEditForm')->name('showUserEditForm');
    Route::post('user/{id}/edit', 'UserController@edit')->name('editUser');
    Route::post('user/delete', 'UserController@delete')->name('deleteUser');
    Route::post('user/activate', 'UserController@activate')->name('activateUser');
    Route::post('user/deactivate', 'UserController@deactivate')->name('deactivateUser');
});