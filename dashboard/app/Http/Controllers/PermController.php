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
		$appcode = session('appcode');
		if ($appcode == null)
			return redirect('app');
		else
			return redirect('dashboard/' . $appcode);
	}

	public function app(Request $request)
	{
		$userid = \Auth::user()->id;
		$apps = DB::table('apps')->join('user_app', 'apps.id', '=', 'user_app.appid')
			->where('user_app.userid', $userid)->get();

		foreach ($apps as $ap) {
			$ap->owner = \App\User::find($ap->ownerid);
			$ap->agencies = DB::table('user_app')->join('users', 'users.id', '=', 'user_app.userid')->where('user_app.appid', $ap->id)->get();
			$ap->count = MtHttp::get('app/count_traffic/' . $ap->code);
		}
		
		return view('app/index', [
			'apps' => $apps
		]);
	}

	public function traffic14(Request $request, $appcode)
	{
		$traffic = MtHttp::get('app/traffic14/' . $appcode);
		return $traffic;
	}

	public function setup_status(Request $request, $appcode)
	{
		$res = MtHttp::getRaw('app/status/' . $appcode);
		return $res . '';
	}

	public function getPageview(Request $request, $appcode)
	{
		$res = MtHttp::getRaw('app/getpageview/' . $appcode);
		return $res . '';
	}

	public function getSignup(Request $request, $appcode,$startime, $endtime)
	{


		$queryurl = 'app/getsignup/' . $appcode;
			$pieces = explode("-", $startime);
			$sts = strtotime($pieces[1] . '/' . $pieces[2] . '/' . $pieces[0]);

			$pieces = explode("-", $endtime);
			$ets = strtotime($pieces[1] . '/' . $pieces[2] . '/' . $pieces[0]);
			$queryurl .= '/' . $sts . '/' . $ets;

		$res = MtHttp::getRaw($queryurl);
		return $res . '';
	}
	
	public function count_traffic(Request $request, $appcode)
	{
		$res = MtHttp::get('app/count_traffic/' . $appcode);
		return $res . '';
	}

	public function counter(Request $request, $appcode)
	{
		$res = MtHttp::get('api/counter/' . $appcode);
		return $res . '';
	}

	public function getedit(Request $request, $appcode)
	{
		$ap = DB::table('apps')->where('code', $appcode)->first();
		if ($ap == null) abort(500, 'cannot find app: ' . $appcode);

		$ap->owner = \App\User::find($ap->ownerid);
		$ap->agencies = DB::table('user_app')->join('users', 'users.id', '=', 'user_app.userid')->where('user_app.appid', $ap->id)->get();

		return view('app/edit', ['ap' => $ap, 'appcode' => $appcode]);
	}

	public function postedit(Request $request, $appcode)
	{
		$name = $request->input('name');
		$url = $request->input('url');
		
		if ($name == null || $name == '')
			abort(500, 'name must not be empty');

		$ap = DB::table('apps')->where('apps.code', $appcode)->first();
		if ($ap == null) abort(500, 'cannot find app: ' . $appcode);

		DB::table('apps')->where('id', $ap->id)->update(['name' => $name, 'url'=>$url]);

		return redirect('/');
	}

	public function set(Request $request, $appcode, $userid)
	{
		$uid = \Auth::user()->id;
		$status = Access::setPerm($uid, $userid, $appcode, $request->input('can_perm'), $request->input('can_struct'), $request->input('can_report'));
		if ($status == 0)
			return new Response();
		else abort(403, 'Unauthorized action');
	}

	public static function createApp($uid, $name, $url ='')
	{
		if ($name == null || $name == '') abort(500, 'name must not be empty');

		// lock thread
		$out = AppCodeGen::alloc($name);
		$code = $out['code'];
		$mutex = $out['lock'];
		DB::table('apps')->insert(array(
				'name' => $name,
				'code' => $code,
				'ownerid' => $uid,
				'url'=>$url
			)
		);

		// unlock thread
		AppCodeGen::used($mutex);

		// create role for owner
		Access::setPerm($uid, $uid, $code, 1, 1, 1);
		// init app in backend
		MtHttp::get('app/init/' . $code);
		return $code;
	}

	public function create(Request $request)
	{
		$uid = \Auth::user()->id;
		$name = $request->input('name');
		$url = $request->input('url');
		$code = self::createApp($uid, $name,$url);
		return new Response($code);
	}

	public function delete(Request $request, $appcode, $userid)
	{
		$uid = \Auth::user()->id;
		$status = Access::deletePerm($uid, $userid, $appcode);
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
		if ($user == null) abort(500, 'user not found: ' . $email);
		$userid = $user->id;
		if ($userid == null)
			abort(500, 'cannot find user with email ' . $email);

		$status = Access::setPerm($uid, $userid, $appcode, null, null, null);
		if ($status == 0)
			return new Response();
		else abort(403, 'Unauthorized action');
	}
        
    public function manage(Request $request, $appcode){
        $ap = DB::table('apps')->where('code', $appcode)->first();
        if ($ap == null) abort(500, 'cannot find app: ' . $appcode);

        $ap->owner = \App\User::find($ap->ownerid);
        $ap->agencies = DB::table('user_app')->join('users', 'users.id', '=', 'user_app.userid')->where('user_app.appid', $ap->id)->get();
        $your_role = '';
        if($ap->agencies){
            foreach($ap->agencies as $ag){
                if($ag->appid != $ap->id || $ag->userid != $ap->ownerid){
                    continue;
                }
                if($ag->can_perm){
                    $your_role = 'Full control';
                    continue;
                }
                if($ag->can_struct){
                    $your_role = 'Edit app structure';
                    continue;
                }
                if($ag->can_report){
                    $your_role = 'Create or edit reports';
                    continue;
                }
            }
        }
        $status = $this->setup_status($request, $appcode) ? 'Connected' : 'Not connected';
        $traffic = $this->count_traffic($request, $appcode);
        return view('app/manage', [
            'ap' => $ap, 
            'appcode' => $appcode,
            'status' => $status,
            'traffic' => $traffic,
            'your_role' => $your_role,
        ]);
    }
    
    public function postadd(Request $request, $appcode)
    {
        $name = $request->input('name');
        $url = $request->input('url');

        if ($name == null || $name == '')
            abort(500, 'name must not be empty');

        $ap = DB::table('apps')->where('apps.code', $appcode)->first();
        if ($ap == null) abort(500, 'cannot find app: ' . $appcode);

        DB::table('apps')->where('id', $ap->id)->update(['name' => $name, 'url'=>$url]);
        return json_encode([
            'success' => true, 
            'name' => $name,
            'url' => $url
        ]);
    }
}