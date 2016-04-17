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

Route::post('/actiontype/create', 'TypeController@create');

Route::get('/home/setup_status', 'HomeController@setup_status');

Route::get('/home/counter', 'HomeController@counter');

Route::controller('segment', 'SegmentController');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('/helper/setup_status', 'HelperController@setup_status');

Route::controller('user', 'UserController');
