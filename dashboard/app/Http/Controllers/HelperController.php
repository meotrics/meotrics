<?php namespace App\Http\Controllers;

use App\Util\MtHttp;

class HelperController extends Controller {

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function setup_status()
  {
    $res = MtHttp::get('api/status/' . \Request::get('app_id'));
    return $res;
  }

}
