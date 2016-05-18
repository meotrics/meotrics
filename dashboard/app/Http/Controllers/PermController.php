<?php namespace App\Http\Controllers;

use App\Util\Access;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Util\MtHttp;

class PermController extends Controller
{
	public function __construct(Request $request) {
		$this->request = $request;
		$this->middleware('auth');
	}

	public function index(Request $request, $appid, $id)
	{
		$userid = \Auth::user()->id;
		$apps = DB::table('apps')->join('user_app', 'apps.id', '=', 'user_app.appid')
				->where('user_app.userid', $userid)
				->where('user_app.can_perm', 1).get();
		foreach($apps as $ap)
		{
			$ap->owner = \App\User::find($ap->ownerid);
			$ap->agencies = DB::table('user_app')->join('users', 'users.id', '=', 'user_app.userid')->where('user_app.appid',$ap->id ).get();
		}

		return view('app/index', [
			'apps'=> $apps
		]);
	}

	public function set(Request $request, $appid, $userid)
	{
		$uid = \Auth::user()->id;
		$status =Access::setPerm($uid,$userid,$appid,$request->input('can_perm'),$request->input('can_struct'),$request->input('can_report'));
		if($status == 0)
			return new Response();
		else abort(403, 'Unauthorized action');
	}
}
