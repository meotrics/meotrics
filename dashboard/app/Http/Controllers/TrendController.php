<?php
namespace App\Http\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class TrendController extends Controller {


	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		return view('trend/index');
	}

	public function query(Request $request)
	{

		// use key 'http' even if you send the request to https://...
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/json\r\n",
				'method'  => 'POST',
				'content' => json_encode(array(
					'event' => $request->input('typeid') ,
					'object' => $request->input('object'),
					'operation' => $request->input('operation'),
					'param' => $request->input('param')
					)
				),
			),
		);
		$context  = stream_context_create($options);
		$result = json_decode(file_get_contents('http://127.0.0.1:2108/trend/1', false, $context));
		$trendid = $result->_id;
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'GET',

			),
		);
		$context = stream_context_create($options);
		$result= file_get_contents('http://127.0.0.1:2108/trend/query/1/' . $trendid, false, $context);

		return $result;

	}

	public function show()
	{
		return "";
	}

}
