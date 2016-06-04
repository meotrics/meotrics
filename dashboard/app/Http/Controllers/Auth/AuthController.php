<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Util\MtHttp;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
	private static $hashalgo;

	public static function init()
	{
		self::$hashalgo = 'sha384';
	}
	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'getLogout']);
	}

	public function getReset($request, $email, $time, $salt, $hash)
	{
		if($time > time()) abort(500, 'wrong time');
		$diff = (time() - $time);

		if($diff > 1800) // 30 min -> valid
		abort(500, 'expired time');

		$valid = $this->validLink($email, $time, $salt, $hash);
		if($valid == false)
		abort(500, 'wrong hash');

		// check in db
		$user = DB::table('users')->where('email', $email)->first();
		if(isset($user->resetpwhash) && strcmp($user->resetpwhash, $hash) == 0)
		{
			return view('auth/reset');
		}
	}

	public function reset($request, $email, $time, $salt, $hash, $newpass)
	{
		if($time > time()) abort(500, 'wrong time');
		$diff = (time() - $time);

		$valid = $this->validLink($email, $time, $salt, $hash);
		if($valid == false)
		abort(500, 'wrong hash');

		// check in db
		$user = DB::table('users')->where('email', $email)->first();
		if(isset($user->resetpwhash) && strcmp($user->resetpwhash, $hash) == 0)
		{
			$user = DB::table('users')->where('email', $email)->update(['resetpwhash' => null]);
			if($diff > 1800) // 30 min -> valid
			{
				
				abort(500, 'expired time');
			}

			$user = DB::table('users')->where('email', $email)->update(['password' => bcrypt($newpass)]);
			return view('/auth/login');
		}
	}

	public function generatePasswordReset($email)
	{
		//check db to see if password has send
		$user = DB::table('users')->where('email', $email)->first();
		if($user->resetpwhash != null )
		{
			return;
		} else 
		{
			$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$string = '';
 		for ($i = 0; $i < 10; $i++) {
      		$string .= $characters[rand(0, strlen($characters) - 1)];
 		}

		$param = urlencode($email) . '/' . time() . '/' . $string;
		$hash = hash(self::$hashalgo, $param, FALSE);
		$host = env('HOSTNAME', 'app.meotrics.com');

			// store hash into db
			$user = DB::table('users')->where('email', $email)->update(['resetpwhash' => $hash]);

			// send reset password mail
			MailSender::send($email, 'resetpw', [link=> "https://" + $host . '/auth/reset/' . $param . '/' . $hash ]);
			return;
		}
	}

	public function validLink($email, $time, $salt, $hash)
	{
		$myparam = urlencode($email) . '/' . $time . '/' . $salt;
		$myhash = hash(self::$hashalgo, $param);
		if(strcmp($myhash, $hash) == 0) // valid
		{
			return true;
		}
		return false;
	}

	public function getConfirmLink($email)
	{
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$string = '';
 		for ($i = 0; $i < 10; $i++) {
      		$string .= $characters[rand(0, strlen($characters) - 1)];
 		}

		$param = urlencode($email) . '/' . time() . '/' . $string;

		$hash = hash(self::$hashalgo, $param, FALSE);
		$host = env('HOSTNAME', 'app.meotrics.com');
		return "https://" + $host . '/auth/confirm/' . $param . '/' . $hash;
	}

	public function googlesignin(Request $request)
	{
		$tokenid = $request->input('id_token');
		$url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=" . $tokenid;

		$options = array('http' => array(
			'header' => "Content-type: application/json\r\n",
			'method' => 'GET'
		));
		$context = stream_context_create($options);

		$output = file_get_contents($url, false, $context);
		if (substr_compare($http_response_header[0], "200 OK", strlen($http_response_header[0]) - strlen("200 OK"), strlen("200 OK")) == 0) {
			$data = MtHttp::json_decodeEx($output);
			$user = DB::table('users')->where('email', $data->email)->first();

			if ($user == null) {
				// send a confirm mail
				$userid = DB::table('users')->insertGetId(['password' => '',
					'email' => $data->email,
					'name' => $data->name,
					'status' => 10,
					'created_at' => date("Y"),
					'updated_at' => date("Y"),
					'verified' => 1]);
			} else {
				// update verified token
				DB::table('users')->where('email', $data->email)->update(['verified'=> 1]);
				$userid = $user->id;
			}

			\Auth::loginUsingId($userid);
			return;
		} else {
			abort(500, 'wrong tokenid');
		}
	}

}
AuthController::init();