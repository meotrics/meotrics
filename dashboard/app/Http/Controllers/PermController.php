<?php namespace App\Http\Controllers;

use App\Util\Access;
use App\Util\AppCodeGen;
use App\Util\MtHttp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PermController extends Controller
{
	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->middleware('auth');
	}

	public function index(Request $request)
	{
		$userid = \Auth::user()->id;
		$apps = DB::table('apps')->join('user_app', 'apps.id', '=', 'user_app.appid')
			->where('user_app.userid', $userid)
			->where('user_app.can_perm', 1)->get();

		foreach ($apps as $ap) {
			$ap->owner = \App\User::find($ap->ownerid);
			$ap->agencies = DB::table('user_app')->join('users', 'users.id', '=', 'user_app.userid')->where('user_app.appid', $ap->id)->get();
		}

		return view('app/index', [
			'apps' => $apps
		]);
	}

	public function edit(Request $request, $appcode)
	{
		$ap = DB::table('apps')->where('apps.code', $appcode)->first();
		if ($ap == null) abort(500, 'cannot find app: ' . $appcode);

		$ap->owner = \App\User::find($ap->ownerid);
		$ap->agencies = DB::table('user_app')->join('users', 'users.id', '=', 'user_app.userid')->where('user_app.appid', $ap->id)->get();

		return view('app/edit', ['ap' => $ap, 'appcode' => $appcode]);
	}

	public function set(Request $request, $appid, $userid)
	{
		$uid = \Auth::user()->id;
		$status = Access::setPerm($uid, $userid, $appid, $request->input('can_perm'), $request->input('can_struct'), $request->input('can_report'));
		if ($status == 0)
			return new Response();
		else abort(403, 'Unauthorized action');
	}

	public function create(Request $request)
	{
		$uid = \Auth::user()->id;
		$name = $request->input('name');
		if ($name == null || $name == '') abort(500, 'name must not be empty');

		// lock thread
		$out = AppCodeGen::alloc($name);
		$code = $out['code'];
		$mutex = $out['lock'];
		$appid = DB::table('apps')->insertGetId(array(
				'name' => $name,
				'code' => $code,
				'ownerid' => $uid
			)
		);

		// unlock thread
		AppCodeGen::used($mutex);


		// create role for owner
		Access::setPerm($uid, $uid, $appid, 1, 1, 1);

		// init app in backend
		MtHttp::get('app/init/' . $code);

		return new Response($code);
	}

	public function delete(Request $request, $appid, $userid)
	{
		$uid = \Auth::user()->id;
		$status = Access::deletePerm($uid, $userid, $appid);
		if ($status == 0)
			return new Response();
		else abort(403, 'Unauthorized action');
	}

	public function add(Request $request, $appcode)
	{
		$email = $request->input('email');
		$uid = \Auth::user()->id;
		//get userid from email
		$user = DB::table('users')->where('email', $email)->first();
		if($user == null) abort(500, 'user not found: ' . $email);
		$userid = $user->id;
		if ($userid == null)
			abort(500, 'cannot find user with email ' . $email);
		
		$status = Access::setPerm($uid, $userid, $appcode, null, null, null);
		if ($status == 0)
			return new Response();
		else abort(403, 'Unauthorized action');
	}

}
