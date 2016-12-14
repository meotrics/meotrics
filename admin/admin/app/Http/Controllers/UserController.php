<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class UserController extends Controller
{
    //
    public function index(){
        return view("user/index");
    }

    public function getListuser(){
        $data = DB::select('select `email`,`name`,`phone`,`banned`,`created_at`,`expired` from `users` order by id desc');
        return $data;
    }

    public function banUser($email,$status){
        $data = DB::update('update `users` set `banned` ='.$status.' where `email`="'.$email.'"');
        return $data;
    }

    public function banReason($email,$status,$message){
        $data = DB::select('insert into `user_ban` (`email`,`ban_status`,`reason`,`created_at`) value("'.$email.'",'.$status.',"'.$message.'",'.time().')');
        return $data;
    }

}
