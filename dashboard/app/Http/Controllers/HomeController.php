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
	private $parser;

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
		$deltat = $request->input('_deltat', 0);
		$type = $request->input('_typeid');

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

		$req = [
			'_typeid' => $type,
			'_ip' => $ip,
			'_browserid' => $ua->ua->family,
			'_browserversion' => $ua->ua->major . "." . $ua->os->minor,
			'_osid' => $ua->os->family,
			'_osversion' => $ua->os->major . '.' . $ua->os->minor,
			'_deviceid' => $ua->device->family,
			'_devicetype' => $devicetype,
			'_referrer' => $referrer,
			'_screenres' => $screenres,
			'_url' => $request->server('HTTP_REFERER'),
			'_language' => $request->server('HTTP_ACCEPT_LANGUAGE'),
			'_deltat' => $deltat
		];

		foreach ($input as $k => $v) {
			if (substr($k, 0, 1) != '_')
				$req[$k] = $v;
		}
		return $req;
	}

	public function track(Request $request, $appid)
	{
		$response = new Response();
		$req = $this->trackBasic($request);
		$req['_mtid'] = $this->getMtid($request, $appid, $response);
		MtHttp::post('r/' . $appid, $req);
		return $response;
	}

	public function code(Request $request, $appid)
	{
		$res = new Response();

		// record an pageview
		$req = $this->trackBasic($request);
		$req['_mtid'] = $this->getMtid($request, $appid, $res);
		$req['_typeid'] = 'pageview';

		$code = $this->loadCode($appid);
		$actionid = MtHttp::post('r/' . $appid, $req);
		$code = str_replace('$ACTIONID$', $actionid, $code);
		$res->setContent($code);
		$res->header('Content-Type', 'application/javascript');
		return $res;
	}

	private function fix(Request $request, $appid, $actionid)
	{
		$response = new Response();
		$req = $this->trackBasic($request);
		$req['_mtid'] = $this->getMtid($request, $appid, $response);
		MtHttp::post('f/' . $appid . '/' . $actionid, $req);
		return $response;
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
		$mtid = $this->getMtid($request, $appid, $response);

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
