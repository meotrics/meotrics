<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{

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


	public function googlelogin(Request $request)
	{
		$tokenid = $request->input('tokenid');


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
				$user->id = DB::table('users')->insertGetId(['password' => '',
					'email' => $data->email,
					'name' => $data->name,
					'status' => 10,
					'created_at' => date("Y"),
					'updated_at' => date("Y"),
					'verified' => 1]);
			} else {
				// update verified token
				DB::table('users')->where('email', $data->email)->update('verified', 1);

			}

			\Auth::loginUsingId($user->id);
			return;
		} else {
			abort(500, 'wrong tokenid');
		}
	}

}
