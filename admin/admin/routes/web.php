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
Route::get('/user', 'UserController@index');
Route::get('/app',  'AppController@index');


//api
Route::get('api/listuser','UserController@getListuser');
Route::get('api/listappbyemail/{email}','AppController@getListappbyemail');
Route::get('api/listapp','AppController@getListapp');
