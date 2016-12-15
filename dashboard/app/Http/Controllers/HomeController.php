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
 		if(Access::can_view($request->user()->id, $appid) == false) abort(500,'Permission Denied');
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
		$url_time = '';
		if (isset($st)) {
			$pieces = explode("-", $st);
			$sts = strtotime($pieces[1] . '/' . $pieces[2] . '/' . $pieces[0]);

			$pieces = explode("-", $et);
			$ets = strtotime($pieces[1] . '/' . $pieces[2] . '/' . $pieces[0]);
			$queryurl .= '/' . $sts . '/' . $ets;
			$url_time = '/'. $sts . '/' . $ets;
		}else{
			$sts = time() - 86400*7;
			$ets = time();
			$url_time = '/'. $sts . '/' . $ets;
		}
		$dashboard =  new \stdClass;
//		$dashboard = MtHttp::get($queryurl);

		// labels
		$url_get_labels = 'dashboard/labels'.$url_time;
		$labels = MtHttp::get($url_get_labels);
		$dashboard->labels = $labels;

		// revenue
		$url_get_revenue = 'dashboard/revenues/' . $appid.$url_time;
		$revenues = MtHttp::get($url_get_revenue);
		$dashboard->revenues = $revenues->revenues;
		$dashboard->n_purchases = $revenues->n_purchases;

		//traffic24
		$url_get_traffic24 = 'dashboard/gettraffic24/' . $appid;
		$revenues = MtHttp::get($url_get_traffic24);
		$dashboard->traffic24 = $revenues->traffic24;
		$dashboard->traffic24labels = $revenues->traffic24labels;

		// today visitor
//		/app/getpageview
		$url_get_visitor = 'app/getpageview/' . $appid;
		$visitor = MtHttp::get($url_get_visitor);
		$dashboard->n_new_visitor = $visitor->newVisitors;
		$dashboard->n_returning_visitor = $visitor->returningVisitors;


		//get signup
		$url_get_signup = 'app/getsignup/' . $appid .$url_time;;
		$signup = MtHttp::get($url_get_signup);
		$dashboard->n_new_signup = $signup->signup;

		//get getgrowthrate
		$url_get_growthrate = 'dashboard/getgrowthrate/' . $appid.$url_time;
		$growthrate = MtHttp::get($url_get_growthrate);
		$dashboard->usergrowth_rate = $growthrate;

		//retention_rate
		$url_get_retentionrate = 'dashboard/retentionrate/' . $appid;
		$retentionrate = MtHttp::get($url_get_retentionrate);
		$dashboard->retention_rate = $retentionrate;

		//gettotalrevenue
		$url_get_totalrevenue = 'dashboard/gettotalrevenue/'. $appid.$url_time;
		$totalrevenue = MtHttp::get($url_get_totalrevenue);
		$dashboard->n_avgcartsize = $totalrevenue->n_avgcartsize;
		$dashboard->total_revenue = $totalrevenue->total_revenue;
		$dashboard->revenue_per_customer = $totalrevenue->revenue_per_customer;

		//conversionratetime
		$url_get_conversionratetime = 'dashboard/conversionratetime/' . $appid;
		$conversionratetime = MtHttp::get($url_get_conversionratetime);
		$dashboard->conversion_rate = $conversionratetime;


		return view('home', ['dashboard' => $dashboard, 'starttime' => $st, 'endtime' =>$et]);//->withCookie(cookie()->forget('mtid'));
	}
}
