<?php namespace App\Http\Controllers;

use App\Util\MtHttp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mobile_Detect;
use UAParser\Parser;
use Auth;
use App\Util\Access;

class HomeController extends Controller
{
    private $parser;
	public function __construct()
	{
		$this->parser = Parser::create();
		$this->middleware('auth');
	}

	public function postCurrentapp(Request $request, $appid)
	{
        if(Access::can_view($request->user()->id, $appid) == false) abort(500,'Permission Denied');
		$response = new Response();
		return $response->withCookie(cookie('currentappid', $appid, 2147483647, '/' . $appid . '/'));
	}

	public function postCurrenttime(Request $request, $appid)
	{
 if(Access::can_view($request->user()->id, $app_id) == false) abort(500,'Permission Denied');
		$response = new Response();
		$st = $request->input('startTime', null);
		$et = $request->input('endTime', null);
		$response->withCookie(cookie('currentdashboardstarttime', $st, 9147483, "/dashboard/$appid"));
		$response->withCookie(cookie('currentdashboardendtime', $et, 9147483, "/dashboard/$appid"));
		return $response;
	}

	public function index(Request $request, $appid)
	{
		// must login first
		//if (false == Auth::check()) return redirect('auth/login');//->withCookie(cookie()->forget('mtid'));
        if(false == Access::can_view(Auth::user()->id, $appid)) abort(500, "Permission denied");
		$st = $request->cookie('currentdashboardstarttime');
		$et = $request->cookie('currentdashboardendtime');

		$queryurl = 'dashboard/' . $appid;
		if (isset($st)) {
			$pieces = explode("-", $st);
			$sts = strtotime($pieces[1] . '/' . $pieces[2] . '/' . $pieces[0]);

			$pieces = explode("-", $et);
			$ets = strtotime($pieces[1] . '/' . $pieces[2] . '/' . $pieces[0]);
			$queryurl .= '/' . $sts . '/' . $ets;
		}

		$dashboard = MtHttp::get($queryurl);
		return view('home', ['dashboard' => $dashboard, 'starttime' => $st, 'endtime' =>$et]);//->withCookie(cookie()->forget('mtid'));
	}
}
