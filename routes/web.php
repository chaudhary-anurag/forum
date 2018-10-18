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
Auth::routes();
Route::get('/', function () {
    return view('welcome');
});
Route::get('/profiles/{user}/notifications','UserNotificationsController@index');

Route::delete('/profiles/{user}/notifications/{notification}','UserNotificationsController@destroy');

Route::post('/threads/{channel}/{thread}/subscriptions','ThreadSubscriptionsController@store')->middleware('auth');
Route::get('/threads/create','ThreadController@create');

Route::delete('/threads/{channel}/{thread}/subscriptions','ThreadSubscriptionsController@destroy')->middleware('auth');

Route::get('/threads','ThreadController@index')->name('threads');
Route::get('/home', 'HomeController@index');

Route::get('/threads/{channel}','ThreadController@index');


Route::get('/threads/{channel}/{thread}','ThreadController@show');
Route::delete('/threads/{channel}/{thread}','ThreadController@destroy');

Route::post('/threads','ThreadController@store')->middleware('must-be-confirmed');

Route::get('/threads/{channel}/{thread}/replies','ReplyController@index');

Route::post('/threads/{channel}/{thread}/replies','ReplyController@store');

Route::delete('/replies/{reply}','ReplyController@destroy');

Route::patch('/replies/{reply}','ReplyController@update');

Route::post('/replies/{reply}/favourites','FavouriteController@store');

Route::delete('/replies/{reply}/favourites','FavouriteController@destroy');

Route::get('/profiles/{user}','ProfileController@show')->name('profile');

Route::get('/register/confirm','Auth\RegisterConfirmationController@index')->name('register.confirm');

Route::get('api/users','Api\UsersController@index');
Route::post('api/users/{user}/avatar','Api\UserAvatarController@store')->middleware('auth')->name('avatar');