<?php namespace App\Http\Controllers;

use App\Util\MtHttp;

class UserController extends Controller {

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function getProfile()
  {
    return view('user/profile');
  }
  public function postProfile()
  {
    $updates = array();
    $updates[\Request::get('name')] = \Request::get('value');
    $user = \Auth::user();
    $user->fill($updates);
    if($user->save()){
      return 'true';
    } else {
      abort(409);
    }

  }

}
