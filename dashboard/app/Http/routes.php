<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');

Route::get('home', 'HomeController@index');

Route::controller('trend', 'TrendController');

Route::resource('actiontype', 'TypeController');

Route::post('/actiontype/{appid}/create', 'TypeController@create');

Route::get('/home/{appid}/setup_status', 'HomeController@setup_status');

Route::get('/home/{appid}/counter', 'HomeController@counter');

Route::get('/segment/{appid}/execute', 'SegmentController@getExecute');
Route::get('/segment/{appid}/index', 'SegmentController@getIndex');
Route::get('/segment/{appid}/create', 'SegmentController@getCreate');
Route::get('/segment/{appid}/update/{id}', 'SegmentController@getUpdate');
Route::post('/segment/{appid}/write', 'SegmentController@postWrite');
Route::delete('/segment/{appid}/remove/{id}', 'SegmentController@deleteRemove');
Route::get('/segment/{appid}/chartonefield', 'SegmentController@getChartonefield');
Route::get('/segment/{appid}/charttwofields', 'SegmentController@getCharttwofields');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('/helper/setup_status', 'HelperController@setup_status');

Route::controller('user', 'UserController');
