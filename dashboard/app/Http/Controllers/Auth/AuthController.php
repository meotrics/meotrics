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

	}

	public function resent(Request $request)
	{
		if (\Auth::user() == null) return redirect('auth/login');
		$userid = \Auth::user()->id;
		// generate new hash
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$string = '';
		for ($i = 0; $i < 10; $i++) {
			$string .= $characters[rand(0, strlen($characters) - 1)];
		}

		$user = DB::table('users')->where('id', $userid)->first();
		if($user == null) abort(500, "user not found");
		$param = urlencode($user->email) . '/' . time() . '/' . $string;
		$hash = hash(self::$hashalgo, $param, FALSE);

		DB::table('users')->where('id', $userid)->update(['resetpwhash' => $hash]);

		//gui mail chao mung
		$host = env('HOSTNAME', 'app.meotrics.com');
		MailSender::send($user->email, 'registry', ['link' => "https://" . $host . '/auth/confirm/' . $param . '/' . $hash]);

		return 'sent';
	}

	public function register(Request $request)
	{
		$isAdmin = $request->input('radio') == 1;
		$email = $request->input('email');

		// create a new user
		// generate new hash
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$string = '';
		for ($i = 0; $i < 10; $i++) {
			$string .= $characters[rand(0, strlen($characters) - 1)];
		}

		$param = urlencode($email) . '/' . time() . '/' . $string;
		$hash = hash(self::$hashalgo, $param, FALSE);

		// make sure user not existed
		$userid = DB::table('users')->where('email', $email)->first();
		if($userid !== null)
		{
			return view('auth/register', ['error'=> "Email '$email' has already existed in the system. If you forgot your password, please follow the instruction in login page."]);
		}

		$userid = DB::table('users')->insertGetId(['password' => '',
			'email' => $email,
			'status' => 10,
			'created_at' => date("Y"),
			'updated_at' => date("Y"),
			'verified' => 0,
			'resetpwhash' => $hash]);

		//gui mail chao mung
		$host = env('HOSTNAME', 'app.meotrics.com');
		MailSender::send($email, 'registry', ['link' => "https://" . $host . '/auth/confirm/' . $param . '/' . $hash]);
		\Auth::loginUsingId($userid);
		if ($isAdmin) {
			$appname = $request->input('sitename');
			$appurl = $request->input('siteurl');

			// create new app
			$code = PermController::createApp($userid, $appname, $appurl);
			// jump right to dashboard

			return redirect('dashboard/' . $code);
		} else {
			return redirect('app/');
		}
	}

	public function getReset(Request $request, $email, $time, $salt, $hash)
	{
		if ($time > time()) return view("errors.500", ['error'=> 'wrong time']);
		$diff = (time() - $time);

		$valid = $this->validLink($email, $time, $salt, $hash);
		if ($valid == false)
			return view("errors.500", ['error'=>'wrong hash']);


		if ($diff > 1800) // 30 min -> valid
		{
			$user = DB::table('users')->where('email', $email)->update(['resetpwhash' => null]);
			return view("errors.500", ['error'=>'expired time']);
		}

		// check in db
		$user = DB::table('users')->where('email', $email)->first();
		if (isset($user->resetpwhash) && strcmp($user->resetpwhash, $hash) == 0) {
			return view('auth/reset');
		}
	}

	public function getConfirm(Request $request, $email, $time, $salt, $hash)
	{
		if ($time > time()) return view("errors.500", ['error'=>'wrong time']);

		$valid = $this->validLink($email, $time, $salt, $hash);
		if ($valid == false)
			return view("errors.500", ['error'=> 'wrong hash']);

		// check in db
		$user = DB::table('users')->where('email', $email)->first();

		// email ton tai trong he thong
		if ($user == null)
			return view("errors.500", ['error'=> 'User not found']);

		if ($user->verified == 1)
			return view("errors.500", ['error'=> 'Email already verified']);

		if (isset($user->resetpwhash) && strcmp($user->resetpwhash, $hash) == 0) {
			return view('auth.newpw', ['confirm'=> true]);
		}
		return view("errors.500", ['error'=> "Wrong user token"]);
	}

	public function confirm(Request $request, $email, $time, $salt, $hash)
	{
		$password = $request->input('password');
		if ($time > time()) return view("errors.500", ['error'=> 'wrong time']);

		$valid = $this->validLink($email, $time, $salt, $hash);
		if ($valid == false)
			return view('auth.newpw', ['error' => 'Wrong hash']);

		// check in db
		$user = DB::table('users')->where('email', $email)->first();

		// email ton tai trong he thong
		if ($user == null)
			return view('auth.newpw', ['error' => 'User not found']);

		if ($user->verified == 1)
			return view('auth.newpw', ['error' => 'Email already verified']);

		if (isset($user->resetpwhash) && strcmp($user->resetpwhash, $hash) == 0) {
			DB::table('users')->where('email', $email)->update(['resetpwhash' => null]);
			$diff = (time() - $time);
			if ($diff > 86400) //  1 day -> valid
			{
				return view('auth.newpw', ['error' => 'Expired time']);
			}

			DB::table('users')->where('email', $email)->update(['password' => bcrypt($password), 'verified'=>1]);
			\Auth::loginUsingId($user->id);
			return redirect('/app');
		}
		return view('auth.newpw', ['error' => 'Wrong user token']);
	}

	public function newpw(Request $request, $email, $time, $salt, $hash)
	{
		if ($time > time()) return view("errors.500", ['error'=> 'wrong time']);

		$valid = $this->validLink($email, $time, $salt, $hash);
		if ($valid == false)
			return view("errors.500", ['error'=>'wrong hash']);

		// check in db
		$user = DB::table('users')->where('email', $email)->first();

		// email ton tai trong he thong
		if ($user == null)
			return view("errors.500", ['error'=>'User not found']);

		if ($user->verified == 0)
			return view("errors.500", ['error'=>'Email not verified']);

		if ($user->password == null)
			return view("errors.500", ['error'=> "Wrong account type"]);

		if (isset($user->resetpwhash) && strcmp($user->resetpwhash, $hash) == 0) {
			return view('auth.newpw');
		}
		return view("errors.500", ['error'=>"Wrong user token"]);

	}

	public function reset(Request $request, $email, $time, $salt, $hash)
	{
		$password = $request->input('password');
		if ($time > time()) return view("errors.500", ['error'=> 'wrong time']);

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

			if($request->has('newsite'))
			{
				if ($request->input('newsite') == true) {

					$appname = $request->input('sitename');
						$appurl = $request->input('siteurl');

				// create new app
				$code = PermController::createApp($userid, $appname, $appurl);
				// jump right to dashboard

					 return "/dashboard/$code";
				}
			}
			return "/app";
		} else {
			return view("errors.500", ['error'=>'wrong tokenid']);
		}
	}

}

AuthController::init();
