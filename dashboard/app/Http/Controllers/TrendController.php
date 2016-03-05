<?php namespace App\Http\Controllers;

class TrendController extends Controller {


	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		return view('trend/show');
	}

	public function query(Request $request)
	{
		//save a trend
		$client = new GuzzleHttp\Client();
		$res = $client->post('127.0.0.1:2108/trend/1',
		json_encode([
			'event' => $request->input('typeid') ,
			'object' => $request->input('object'),
			'operation' => $request->input('operation'),
			'param' => $request->input('param')
		]));

		$json = json_decode($res->getBody());
		var_dump("$json");
		die;
		$res = $client->post('127.0.0.1:2108/trend/query/1/' . $trendid);

		$json = json_decode($res->getBody());
		var_dump("$json");
	}

	public function show()
	{
		return "";
	}

}
