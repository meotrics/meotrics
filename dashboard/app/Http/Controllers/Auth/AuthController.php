<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PermController;
use App\Util\MailSender;
use App\Util\MtHttp;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// This controller handles the registration of new users, as well as the
// authentication of existing users
class AuthController extends Controller
{
	private static $hashalgo;

	public static function init()
	{
		self::$hashalgo = 'sha384';
	}

	use AuthenticatesAndRegistersUsers;

	public function register(Request $request)
	{
		$isAdmin = $request->input('radio') == 1;
		$email = $request->input('email') == 1;
		
		// create a new user
		// generate new hash
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$string = '';
		for ($i = 0; $i < 10; $i++) {
			$string .= $characters[rand(0, strlen($characters) - 1)];
		}

		$param = urlencode($email) . '/' . time() . '/' . $string;
		$hash = hash(self::$hashalgo, $param, FALSE);
		
		$userid = DB::table('users')->insertGetId(['password' => '',
			'email' => $email,
			'status' => 10,
			'created_at' => date("Y"),
			'updated_at' => date("Y"),
			'verified' => 0,
			'resetpwhash'=> $hash]);

		//gui mail chao mung
		$host = env('HOSTNAME', 'app.meotrics.com');
		MailSender::send($email, 'register', ['link' => "https://" . $host . '/auth/reset/' . $param . '/' . $hash]);
		
		if($isAdmin)
		{
			$appname = $request->input('sitename');
			$appurl = $request->input('siteurl');

			// create new app
			$code = PermController::createApp($userid,$appname, $appurl);
			//nhay thang vao dashboard
			return  redirect('dashboard/' . $code);
		}
		else
		{
			
		}
		
		
	}
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
		if ($time > time()) abort(500, 'wrong time');
		$diff = (time() - $time);

		$valid = $this->validLink($email, $time, $salt, $hash);
		if ($valid == false)
			abort(500, 'wrong hash');


		if ($diff > 1800) // 30 min -> valid
		{
			$user = DB::table('users')->where('email', $email)->update(['resetpwhash' => null]);
			abort(500, 'expired time');
		}

		// check in db
		$user = DB::table('users')->where('email', $email)->first();
		if (isset($user->resetpwhash) && strcmp($user->resetpwhash, $hash) == 0) {
			return view('auth/reset');
		}
	}

	public function getConfirm(Request $request, $email, $time, $salt, $hash)
	{
		if ($time > time()) abort(500, 'wrong time');
		$valid = $this->validLink($email, $time, $salt, $hash);
		if ($valid == false)
			abort(500, 'wrong hash');

		// check in db
		$user = DB::table('users')->where('email', $email)->first();
		if ($user->verified == 1) abort(500, 'expired link');

		return view('auth/confirm', ['email' => $email, 'time' => $time, 'salt' => $salt, 'hash' => $hash]);
	}

	public function confirm($request, $email, $time, $salt, $hash, $password)
	{
		if ($time > time()) abort(500, 'wrong time');
		$valid = $this->validLink($email, $time, $salt, $hash);
		if ($valid == false)
			abort(500, 'wrong hash');

		// check in db
		$user = DB::table('users')->where('email', $email)->first();
		if ($user->verified == 1) abort(500, 'expired link');

		$user = DB::table('users')->where('email', $email)->update(['password' => bcrypt($password), 'verified' => 1]);

		\Auth::loginUsingId($user->id);
		return view('/app');
	}

	public function newpw(Request $request, $email, $time, $salt, $hash)
	{
		if ($time > time()) abort(500, 'wrong time');

		$valid = $this->validLink($email, $time, $salt, $hash);
		if ($valid == false)
			abort(500, 'wrong hash');

		// check in db
		$user = DB::table('users')->where('email', $email)->first();

		// email ton tai trong he thong
		if ($user == null)
			abort(500, 'User not found');

		if ($user->verified == 0)
			abort(500, 'Email not verified');

		if ($user->password == null)
			abort(500, "Wrong account type");

		if (isset($user->resetpwhash) && strcmp($user->resetpwhash, $hash) == 0) {
			return view('auth.newpw');
		}
		abort(500, "Wrong user token");

	}

	public function reset(Request $request, $email, $time, $salt, $hash)
	{
		$password = $request->input('password');
		if ($time > time()) abort(500, 'wrong time');

		$valid = $this->validLink($email, $time, $salt, $hash);
		if ($valid == false)
			return view('auth.newpw', ['error' => 'Wrong hash']);

		// check in db
		$user = DB::table('users')->where('email', $email)->first();

		// email ton tai trong he thong
		if ($user == null)
			return view('auth.newpw', ['error' => 'User not found']);

		if ($user->verified == 0)
			return view('auth.newpw', ['error' => 'Email not verified']);

		if ($user->password == null)
			return view('auth.newpw', ['error' => 'Wrong account type']);

		if (isset($user->resetpwhash) && strcmp($user->resetpwhash, $hash) == 0) {
			$user = DB::table('users')->where('email', $email)->update(['resetpwhash' => null]);
			$diff = (time() - $time);
			if ($diff > 1800) // 30 min -> valid
			{
				return view('auth.newpw', ['error' => 'Expired time']);
			}

			DB::table('users')->where('email', $email)->update(['password' => bcrypt($password)]);
			return view('auth.login');
		}
		return view('auth.newpw', ['error' => 'Wrong user token']);
	}

	public function generatePasswordReset(Request $request)
	{
		$email = $request->input('email');
		$user = DB::table('users')->where('email', $email)->first();

		// email ton tai trong he thong
		if ($user == null)
			return view('/auth/reset', ['error' => "0x1905 User not found"]);

		// nguoi dung da verify
		if ($user->verified == 0)
			return view('/auth/reset', ['error' => "0x0703 Email not verified"]);

		// truong password khac null
		if ($user->password == null)
			return view('/auth/reset', ['error' => "0x4992 Wrong account type"]);

		//check db to see if password has send

		if ($user->resetpwhash != null) {
			return view('/auth/reset', ['error' => "0x2710 Reset link has already been sent"]);
		} else {
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
			MailSender::send($email, 'resetpw', ['link' => "https://" . $host . '/auth/reset/' . $param . '/' . $hash]);
			return view('/auth/reset', ['success' => true]);
		}
	}

	public function validLink($email, $time, $salt, $hash)
	{
		$myparam = urlencode($email) . '/' . $time . '/' . $salt;
		$myhash = hash(self::$hashalgo, $myparam);
		if (strcmp($myhash, $hash) == 0) // valid
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
		return "https://" . $host . '/auth/confirm/' . $param . '/' . $hash;
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
				DB::table('users')->where('email', $data->email)->update(['verified' => 1]);
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