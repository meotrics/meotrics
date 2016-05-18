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

Route::get('/', 'PermController@index');
Route::get('/app/{appid}', 'PermController@set');

Route::post('/app/create','PermController@create' );



//Route::controller('trend', 'TrendController');
Route::get('/trend/{appid}/save', 'TrendController@getSave');
Route::post('/trend/{appid}/currenttime', 'TrendController@postCurrenttime');
Route::post('/trend/{appid}/currentsegment', 'TrendController@postCurrentsegment');
Route::post('/trend/{appid}/currenttrend/{$trendid}', 'TrendController@postCurrenttrend');
Route::get('/trend/{appid}/index', 'TrendController@getIndex');
Route::get('/trend/{appid}/query', 'TrendController@getQuery');
Route::get('/trend/{appid}/create', 'TrendController@getCreate');
Route::post('/trend/{appid}/write', 'TrendController@postWrite');
Route::get('/trend/{appid}/htmloutputs', 'TrendController@getHtmloutputs');
Route::get('/trend/{appid}/update/{id}', 'TrendController@getUpdate');
Route::delete('/trend/{appid}/remove/{id}', 'TrendController@deleteRemove');


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


Route::post('/perm/{appid}/add/{email}', 'PermController@add');
Route::post('/perm/{appid}/{userid}', 'PermController@set');
Route::get('/perm/{appid}/{id}', 'PermController@index');
Route::post('/perm/{appid}/delete/{userid}', 'PermController@delete');
Route::put('/app/create', 'PermController@create');


Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('/helper/setup_status', 'HelperController@setup_status');

Route::controller('user', 'UserController');
