<?php namespace App\Http\Controllers;

use App\Util\MtHttp;
use Illuminate\Support\Facades\Request;

class UserController extends Controller {

  public function __construct()
  {
    $this->middleware('auth');
  }

	public function signout(Request $request)
	{
		return view('auth/signout');
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
  public function getPassword()
  {
    return view('user/password');
  }
  public function postPassword()
  {
    $user = \Auth::user();
    $current_password = \Input::get('current_password');
    $password = \Input::get('password');
    $password_confirmation = \Input::get('password_confirmation');
    if($password != $password_confirmation){
      return \Redirect::to('/user/password')->withErrors('Password does not match');
    }
    if (strlen($current_password) > 0 && !\Hash::check($current_password, $user->password)) {
      return \Redirect::to('/user/password')->withErrors('Please specify the good current password');
    }
    $user->password = \Hash::make($password);
    if($user->save()){
      return \Redirect::to('/user/profile')->with('success', 'Change password successfuly');
    } else {
      return \Redirect::to('/user/password')->withErrors('Error occured, password does not change');
    }
  }


}
