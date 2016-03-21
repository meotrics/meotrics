<?php
namespace App\Http\Controllers;
use App\Util\MtHttp;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class TrendController extends Controller {


	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		$actiontypes = MtHttp::get('actiontype/' . '1');
		return view('trend/index', ['types' => json_encode($actiontypes)]);
	}

	public function query(Request $request)
	{
		$data = array(
			'event' => $request->input('typeid') ,
			'object' => $request->input('object'),
			'operation' => $request->input('operation'),
			'param' => $request->input('param')
		);

		$trendid = MtHttp::post('trend/1', $data);
		$result =MtHttp::get('trend/query/1/' . $trendid);
		return $result;
	}

	public function show()
	{
		return "";
	}

}
