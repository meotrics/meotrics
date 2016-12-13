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
        $data = DB::select('select email,name,phone,verified,created_at from users order by id desc');
        return $data;
    }

}
