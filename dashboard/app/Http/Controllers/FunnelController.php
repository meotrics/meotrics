<?php
/**
 * Created by PhpStorm.
 * User: vietle
 * Date: 6/26/16
 * Time: 10:19 PM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Util\MtHttp;

class FunnelController extends Controller{
    private $request;

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }
    public function index(){
//        $this->layout = View::make('layouts.master');
        return view('funnel/index');
    }
//    public function index(Request $request, $appid)
//    {
//        var_dump("fuck");die;
//        $result = MtHttp::get('actiontype/' . $appid);
//        return view('funnel/index', [
//            'appcode' => $appid,
//            'actiontypes' => $result
//        ]);
//    }
}