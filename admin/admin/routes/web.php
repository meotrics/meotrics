<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/home', 'HomeController@index');
// user

Route::get('/user', 'UserController@index');

// app
Route::get('/app',  'AppController@index');


//api
//user
Route::get('api/listuser','UserController@getListuser');
Route::get('api/banuser/{email}/{status}','UserController@banUser');
Route::get('api/countuser/{appid}/{starttime}/{endtime}','AppController@getCountuserapp');


//ban user
Route::get('api/banreason/{email}/{status}/{message}','UserController@banReason');


// app
Route::get('api/listappbyemail/{email}','AppController@getListappbyemail');
Route::get('api/listapp','AppController@getListapp');
