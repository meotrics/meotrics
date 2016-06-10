<?php

Route::get('/app/edit/{appcode}', 'PermController@getedit');
Route::post('/app/edit/{appcode}', 'PermController@postedit');
Route::get('/', 'PermController@index');
Route::get('/app', 'PermController@app');
Route::post('/app/create','PermController@create' );
Route::get('/dashboard/{appcode}', 'HomeController@index');
Route::post('/perm/{appcode}/add', 'PermController@add');
Route::post('/perm/{appcode}/set/{userid}', 'PermController@set');
Route::post('/perm/{appcode}/delete/{userid}', 'PermController@delete');

Route::get('/app/setup_status/{appcode}', 'PermController@setup_status');
Route::get('/app/count_traffic/{appcode}', 'PermController@count_traffic');
Route::get('/app/traffic14/{appcode}', 'PermController@traffic14');
//Route::controller('trend', 'TrendController');

Route::get('/trend/{appcode}/save', 'TrendController@getSave');
Route::post('/trend/{appcode}/currenttime', 'TrendController@postCurrenttime');
Route::post('/trend/{appcode}/currentsegment', 'TrendController@postCurrentsegment');
Route::post('/trend/{appcode}/currenttrend/{$trendid}', 'TrendController@postCurrenttrend');

Route::get('/trend/{appcode}/query', 'TrendController@getQuery');
Route::get('/trend/{appcode}/create', 'TrendController@getCreate');
Route::post('/trend/{appcode}/write', 'TrendController@postWrite');
Route::get('/trend/{appcode}/htmloutputs', 'TrendController@getHtmloutputs');
Route::get('/trend/{appcode}/update/{id}', 'TrendController@getUpdate');
Route::delete('/trend/{appcode}/remove/{id}', 'TrendController@deleteRemove');
Route::get('/trend/{appcode}/{trendid?}', 'TrendController@getIndex');

Route::get('/actiontype/{appcode}', 'TypeController@index');
Route::post('/actiontype/{appcode}/update/{id}', 'TypeController@update');
Route::get('/actiontype/{appcode}/show/{id}', 'TypeController@show');
Route::get('/actiontype/{appcode}/create', 'TypeController@create');
Route::post('/actiontype/{appcode}/store', 'TypeController@store');
Route::delete('/actiontype/{appcode}/{id}', 'TypeController@destroy');

Route::post('/actiontype/{appid}/create', 'TypeController@create');

Route::get('/segment/{appcode}/execute', 'SegmentController@getExecute');

Route::get('/segment/{appcode}/create', 'SegmentController@getCreate');
Route::get('/segment/{appcode}/update/{id}', 'SegmentController@getUpdate');
Route::post('/segment/{appcode}/write', 'SegmentController@postWrite');
Route::delete('/segment/{appcode}/remove/{id}', 'SegmentController@deleteRemove');
Route::get('/segment/{appcode}/chartonefield', 'SegmentController@getChartonefield');
Route::get('/segment/{appcode}/charttwofields', 'SegmentController@getCharttwofields');
Route::get('/segment/{appcode}/{segid?}', 'SegmentController@getIndex');

Route::post('/auth/googlesignin', 'Auth\AuthController@googlesignin');
Route::get('auth/signout', 'UserController@signout');

Route::get('auth/confirm/{email}/{time}/{salt}/{hash}', 'Auth\AuthController@getConfirm');
Route::post('auth/confirm/{email}/{time}/{salt}/{hash}/{password}', 'Auth\AuthController@confirm');


Route::get('auth/reset', function(){
	return View::make('auth.reset');
});

Route::post('auth/reset', 'Auth\AuthController@generatePasswordReset');
Route::get('auth/reset/{email}/{time}/{salt}/{hash}', 'Auth\AuthController@newpw');
Route::post('auth/reset/{email}/{time}/{salt}/{hash}', 'Auth\AuthController@reset');
Route::post('auth/register', 'Auth\AuthController@register');
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);




Route::controller('user', 'UserController');
