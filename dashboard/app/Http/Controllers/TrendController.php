<?php
namespace App\Http\Controllers;

use App\Util\MtHttp;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class TrendController extends Controller
{


	public function __construct()
	{
		$this->middleware('auth');
	}

	public function getSave(Request $request)
	{
		$data = array(
			'typeid' => $request->input('typeid'),
			'object' => $request->input('object'),
			'operation' => $request->input('operation'),
			'param' => $request->input('param'),
			'order' => intval($request->input('order')),
			'name' => $request->input('name'),
		);
		return MtHttp::post('trend/1', $data);
	}

	public function getList()
	{
		return json_encode(MtHttp::get('trend/' . '1'));
	}

	public function getDelete()
	{

	}

	public function getIndex()
	{
		$actiontypes = MtHttp::get('actiontype/' . '1');
		$trends = MtHttp::get('trend/' . '1');
		return view('trend/index', [
			'types' => json_encode($actiontypes),
			'trends' => json_encode($trends)
		]);
	}

	public function getQuery(Request $request)
	{
		if ($request->input('_id') != null) {
			$trendid = $request->input('_id');
		} else {

			$data = array(
				'typeid' => $request->input('typeid'),
				'object' => $request->input('object'),
				'operation' => $request->input('operation'),
				'param' => $request->input('param'),
				'order' => intval($request->input('order')),
				'name' => 'Draft',
				'_isDraft' => true
			);
			$trendid = MtHttp::post('trend/1', $data);
		}

		$result = MtHttp::get('trend/query/1/' . $trendid);
		return $result;
	}

	public function show()
	{
		return "";
	}

}
