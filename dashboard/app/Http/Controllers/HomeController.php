<?php namespace App\Http\Controllers;

use App\Util\MtHttp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \Mobile_Detect;
use PhpSpec\Exception\Exception;
use UAParser\Parser;

class HomeController extends Controller
{
	public function __construct()
	{
		$this->parser = Parser::create();
		//$this->middleware('auth');
	}

	public function index(Request $request)
	{
		if ($request->user())
			return view('home');
		else
			return redirect('auth/login');
	}

	public function pageView(Request $request)
	{
		$appid = $request->input('_appid');
		$req = $this->trackBasic($request);
		$req['_type'] = 'pageview';
		MtHttp::post('r/' . $appid, json_encode($req));
		return;
	}

	public function pageQuit(Request $request)
	{
		$appid = $request->input('_appid');
		$req = $this->trackBasic($request);
		$req['_type'] = 'pageview';
		MtHttp::post('r/' . $appid, json_encode($req));
		return;
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

		//copy all $input prop that dont startwith _ into $data
		$input = $request->all();
		$data = [];
		foreach ($input as $k => $v) {
			if (substr($k, 0, 1) != '_')
				$data[$k] = $v;
		}


		//get mtid
		$mtid = $request->input('_mtid');
		if ($mtid == null) $mtid = $request->cookie('mtid');
		if ($mtid == null) throw new Exception("mtid is wrong");

		$userid = $request->input('_userid');

		return [
			'type' => $type,
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
			'language' => $request->server('HTTP_ACCEPT_LANGUAGE'),
			'time' => Carbon::now()->toIso8601String(),
			'mtid' => $request->cookie('mtid'),
			'userid' => $userid
		];
	}

	public function track(Request $request)
	{
		$req = $this->trackBasic($request);
		$appid = $request->input('_appid');
		MtHttp::post('/r/' . $appid, json_encode($req));
		return;
	}

	public function identify(Request $request)
	{
		$appid = $request->input('_appid');
		$input = $request->input('_userid');
		$mtid = $request->input('_mtid');

		//get mtid
		if ($mtid == null) $mtid = $request->cookie('mtid');
		if ($mtid == null) throw new Exception("mtid is wrong");

		//copy all $input prop that dont startwith _ into $data
		$data = [];
		foreach ($input as $k => $v) {
			if (substr($k, 0, 1) != '_')
				$data[$k] = $v;
		}

		$req = [
			'userid' => $data['userid'],
			'mtid' => $mtid,
			'data' => $data
		];

		$mtid = MtHttp::post('/i/' . $appid, json_encode($req));
		$response = new Response($mtid);
		return $response->withCookie($mtid);
	}

	public function setup(Request $request)
	{
		$response = new Response();
		$appid = $request->input('_appid');
		$mtid = MtHttp::get('/s/' . $appid);
		$response->withCookie(cookie()->forever('mtid', $mtid));
		return $response;
	}

}
