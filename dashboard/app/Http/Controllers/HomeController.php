<?php namespace App\Http\Controllers;

use App\Util\MtHttp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \Mobile_Detect;
use UAParser\Parser;

class HomeController extends Controller
{

	//private  $parser;
	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->parser = Parser::create();
		//$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('home');
	}

	public function pageView(Request $request)
	{
		$req = $this->trackBasic($request);
		$req['_type'] = 'pageview';
		MtHttp::post('r', json_encode($req));
		return '';
	}

	public function pageQuit(Request $request)
	{
	}

	private function getRemoteIPAddress(Request $request)
	{
		if (null != $request->ip()) return $request->ip();
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
		return $_SERVER['REMOTE_ADDR'];
	}

	private function trackBasic(Request $request)
	{
		$screenres = $request->input('_screenres', '');
		$referrer = $request->input('_referrer', '');
		$ip = $this->getRemoteIPAddress($request);

		$type = $request->input('_type');

		//browser, platform
		$uas = $request->header('User-Agent');
		$ua = $this->parser->parse($uas);

		//device type
		$detect = new Mobile_Detect;
		if ($detect->isTablet($uas))
			$devicetype = 'tablet';
		else if ($detect->isMobile($uas))
			$devicetype = 'tablet';
		else
			$devicetype = 'desktop';

		//input data
		$input = $request->all();
		$data = [];
		foreach ($input as $k => $v) {
			if (substr($k, 0, 1) != '_')
				$data[$k] = $v;
		}

		return [
			'ip' => $ip,
			'browserid' => $ua->ua->family,
			'browserversion' => $ua->ua->major . "." . $ua->os->minor,
			'osid' => $ua->os->family,
			'osversion' => $ua->os->major . '.' . $ua->os->minor,
			'deviceid' => $ua->device->family,
			'devicetype' => $devicetype,
			'referrer' => $referrer,
			'data' => $data,
			'screenres' => $screenres,
			'url' => $request->server('HTTP_REFERER'),
			'language' => $request->server('HTTP_ACCEPT_LANGUAGE')
		];
	}

	public function track(Request $request)
	{
		$req = $this->trackBasic($request);
		MtHttp::post('r', json_encode($req));
		return '';
	}

	public function identify()
	{

	}

	public function setup(Request $request)
	{
		$response = new Response();
		$appid = $request->input('appid');
		$mtid = MtHttp::get('/s/' . $appid);
		$response->withCookie(cookie()->forever('mtid', $mtid));
		return $response;
	}

}
