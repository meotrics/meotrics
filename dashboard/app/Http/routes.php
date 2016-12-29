<?php

Route::get('/', 'PermController@index');


Route::get('/app/edit/{appcode}', 'PermController@getedit');
Route::post('/app/edit/{appcode}', 'PermController@postedit');
Route::get('/app', 'PermController@app');
Route::post('/app/create','PermController@create' );
Route::post('/app/setup_status/{appcode}', 'PermController@setup_status');
Route::post('/app/count_traffic/{appcode}', 'PermController@count_traffic');
Route::post('/app/traffic14/{appcode}', 'PermController@traffic14');
Route::post('/app/getpageview/{appcode}', 'PermController@getPageview');
Route::post('/app/getsignup/{appcode}/{starttime}/{endtime}', 'PermController@getSignup');
Route::get('/app/manage/{appcode}', 'PermController@manage');
Route::post('/app/postadd/{appcode}', 'PermController@postadd');

Route::get('/app/setup_status/{appcode}', 'PermController@setup_status');
Route::get('/app/count_traffic/{appcode}', 'PermController@count_traffic');
Route::get('/app/traffic14/{appcode}', 'PermController@traffic14');

Route::get('/dashboard/{appcode}', 'HomeController@index');
Route::post('/dashboard/{appcode}/currenttime', 'HomeController@postCurrenttime');

Route::post('/perm/{appcode}/add', 'PermController@add');
Route::post('/perm/{appcode}/set/{userid}', 'PermController@set');
Route::post('/perm/{appcode}/delete/{userid}', 'PermController@delete');


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
Route::put('/actiontype/{appcode}/update/{id}', 'TypeController@update');
Route::get('/actiontype/{appcode}/show/{id}', 'TypeController@show');
Route::get('/actiontype/{appcode}/create', 'TypeController@getcreate');
Route::post('/actiontype/{appcode}/store', 'TypeController@store');
Route::get('/actiontype/{appcode}/delete/{id}', 'TypeController@destroy');

Route::get('/segment/{appcode}/execute/{id}', 'SegmentController@getExecute');
Route::get('/segment/{appcode}/refresh/{id}', 'SegmentController@getRefresh');
Route::get('/segment/{appcode}/create', 'SegmentController@getCreate');
Route::get('/segment/{appcode}/update/{id}', 'SegmentController@getUpdate');
Route::post('/segment/{appcode}/write', 'SegmentController@postWrite');
Route::post('/segment/{appcode}/exportexcel', 'SegmentController@postExportexcel');
Route::delete('/segment/{appcode}/remove/{id}', 'SegmentController@deleteRemove');
Route::get('/segment/{appcode}/chartonefield', 'SegmentController@getChartonefield');
Route::get('/segment/{appcode}/charttwofields', 'SegmentController@getCharttwofields');
Route::get('/segment/{appcode}/getChartActionType', 'SegmentController@getChartActionType');
Route::get('/segment/{appcode}/usersbyfield', 'SegmentController@getUsersbyfield');
Route::get('/segment/{appcode}/{segid?}', 'SegmentController@getIndex');

Route::get('/funnel/{appcode}','FunnelController@index');
Route::get('/revenue/{appcode}','RevenueController@index');
Route::get('/marketing/{appcode}','MarketingController@index');
Route::get('/user/profile/{appcode}', 'UserController@profile');
Route::get('/userprofile/{appcode}', 'ListuserController@index');


Route::post('/auth/googlesignin', 'Auth\AuthController@googlesignin');
Route::get('auth/signout', 'UserController@signout');
Route::post('auth/resent', 'Auth\AuthController@resent');
Route::post('auth/reset', 'Auth\AuthController@generatePasswordReset');
Route::get('auth/confirm/{email}/{time}/{salt}/{hash}', 'Auth\AuthController@getConfirm');
Route::post('auth/confirm/{email}/{time}/{salt}/{hash}', 'Auth\AuthController@confirm');
Route::get('auth/reset/{email}/{time}/{salt}/{hash}', 'Auth\AuthController@newpw');
Route::post('auth/reset/{email}/{time}/{salt}/{hash}', 'Auth\AuthController@reset');
Route::post('auth/register', 'Auth\AuthController@register');
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('auth/reset', function(){
	return View::make('auth.reset');
});

Route::controller('user', 'UserController');


Route::get('/error',function(){
	abort(404);
});
