<?php namespace App\Http\Controllers;

use App\Util\MtHttp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use \Mobile_Detect;
use PhpSpec\Exception\Exception;
use UAParser\Parser;

class HomeController extends Controller
{
	private static $code;

	private function loadCode($appid = null)
	{
		// cache mt.min.js in ::$code
		if (HomeController::$code == null) {
			$code = HomeController::$code = Storage::disk('resouce')->get('mt.min.js');
		} else {
			$code = HomeController::$code;
		}

		if ($appid != null) {
			$code = str_replace('$APPID$', $appid, $code);
		}

		return $code;
	}

	public function __construct()
	{
		$this->parser = Parser::create();
		$this->loadCode();
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

	public function track(Request $request, $appid)
	{
		$req = $this->trackBasic($request);
		MtHttp::post('r/' . $appid, json_encode($req));
		return $req;
	}

	private function userSetUp()
	{

	}

	public function code(Request $request, $appid)
	{
		$res = new Response($this->loadCode($appid));
		$res->header('Content-Type', 'application/javascript');
		return $res;
	}

	private function getMtid(Request $request, $appid, Response $response)
	{
		$mtid = $request->cookie('mtid');
		if ($mtid == null) {
			// get new mtid
			$mtid = MtHttp::get('s/' . $appid);
			$response->withCookie(cookie()->forever('mtid', $mtid, '/api/' . $appid));
		}
		return $mtid;
	}

	public function identify(Request $request, $appid)
	{
		$response = new Response();
		$input = $request->input('_userid');
		$mtid = getMtid($request, $appid, $response);

		//copy all $input prop that dont startwith _ into $data
		$data = [];
		foreach ($input as $k => $v) {
			if (substr($k, 0, 1) != '_')
				$data[$k] = $v;
		}

		$req = [
			'mtid' => $mtid,
			'user' => $data
		];

		$mtid = MtHttp::post('i/' . $appid, $req);
		$response->setContent($mtid);
		return $response->withCookie(cookie()->forever('mtid', $mtid, '/api/' . $appid));
	}

	public function clear(Request $request, $appid)
	{
		$response = new Response();
		$response->withCookie(Cookie::forget('mtid', '/api/' . $appid));
		return $response;
	}


}
