<?php namespace App\Http\Controllers;
use App\Util\MtHttp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use UAParser\Parser;

class HomeController extends Controller
{

	private static $parser;
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
		HomeController::$parser = Parser::create();
		$this->middleware('auth');
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

	public function pageView()
	{
	}

	public function pageQuit()
	{
	}

	public function track(Request $request)
	{
		$ua = $request->server('HTTP_USER_AGENT');
		return $ua;
		$ua = $request->header('User-Agent');
		return $ua;
		//$result = $parser->parse($ua)

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
