<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class AppController extends Controller
{
    //
    public function index(){
        return view("app/index");
    }

    public function app(){
        return view("app/app");
    }

    // api
    public function getListapp(){
        $data = DB::select('select * from apps order by id desc');
        return $data;
    }

    public function getListappbyemail($email){
        $id = DB::select('select id from users where email ="'.$email.'"');
        $id = $id[0]->id;
//        var_dump($id);
//        die;
        $data = DB::select('select * from apps where ownerid="'.$id.'"');
        return $data;
}
}
