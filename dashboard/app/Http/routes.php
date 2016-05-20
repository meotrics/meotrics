<?php

Route::get('/app/edit/{appcode}', 'PermController@edit');
Route::get('/', 'PermController@index');
Route::post('/app/create','PermController@create' );
Route::get('/dashboard/{appcode}', 'HomeController@index');
Route::post('/perm/{appcode}/add', 'PermController@add');
Route::post('/perm/{appcode}/set/{userid}', 'PermController@set');
Route::post('/perm/{appcode}/delete/{userid}', 'PermController@delete');


//Route::controller('trend', 'TrendController');
Route::get('/trend/{appcode}/save', 'TrendController@getSave');
Route::post('/trend/{appcode}/currenttime', 'TrendController@postCurrenttime');
Route::post('/trend/{appcode}/currentsegment', 'TrendController@postCurrentsegment');
Route::post('/trend/{appcode}/currenttrend/{$trendid}', 'TrendController@postCurrenttrend');
Route::get('/trend/{appcode}', 'TrendController@getIndex');
Route::get('/trend/{appcode}/query', 'TrendController@getQuery');
Route::get('/trend/{appcode}/create', 'TrendController@getCreate');
Route::post('/trend/{appcode}/write', 'TrendController@postWrite');
Route::get('/trend/{appcode}/htmloutputs', 'TrendController@getHtmloutputs');
Route::get('/trend/{appcode}/update/{id}', 'TrendController@getUpdate');
Route::delete('/trend/{appcode}/remove/{id}', 'TrendController@deleteRemove');


Route::resource('actiontype', 'TypeController');

Route::post('/actiontype/{appid}/create', 'TypeController@create');

Route::get('/segment/{appcode}/execute', 'SegmentController@getExecute');

Route::get('/segment/{appcode}/create', 'SegmentController@getCreate');
Route::get('/segment/{appcode}/update/{id}', 'SegmentController@getUpdate');
Route::post('/segment/{appcode}/write', 'SegmentController@postWrite');
Route::delete('/segment/{appcode}/remove/{id}', 'SegmentController@deleteRemove');
Route::get('/segment/{appcode}/chartonefield', 'SegmentController@getChartonefield');
Route::get('/segment/{appcode}/charttwofields', 'SegmentController@getCharttwofields');
Route::get('/segment/{appcode}/{segid?}', 'SegmentController@getIndex');



Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('/helper/setup_status', 'HelperController@setup_status');

Route::controller('user', 'UserController');
