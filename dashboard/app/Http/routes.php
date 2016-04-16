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

Route::get('api/{appid}/identify', 'HomeController@identify');

Route::get('api/{appid}/track', 'HomeController@track');

Route::get('api/{appid}/clear', 'HomeController@clear');

Route::get('api/{appid}/fix/{actionid}', 'HomeController@fix');

Route::post('/actiontype/create', 'TypeController@create');

Route::get('api/{appid}/code', 'HomeController@code');

Route::controller('segment', 'SegmentController');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('/helper/setup_status', 'HelperController@setup_status');

Route::controller('user', 'UserController');
