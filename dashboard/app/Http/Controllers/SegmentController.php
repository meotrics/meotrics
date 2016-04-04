<?php namespace App\Http\Controllers;

use App\Util\MtHttp;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
	public function getExecute(Request $request)
	{
		$app_id = \Auth::user()->id;
		$query = $request->input('query');
		$id = $request->input('id');

		if ($id == null || $id == -1) {
			$id = MtHttp::post('segment/' . $app_id, ['query' => $query, 'name' => 'Draf']);
		}

		//run querry on field
		//MtHttp::get(':3000/segment/query')


		return json_encode([]);
	}

	public function getIndex()
	{
		$app_id = \Auth::user()->id;

		$props = MtHttp::get('prop/' . $app_id);

		$segments = MtHttp::get('segment/' . $app_id);

		return view('segment/index', [
			'props' => $props,
			'segments' => $segments
		]);
	}

	public function getCreate(){
		$app_id = \Auth::user()->id;

		$actions = MtHttp::get('actiontype/' . $app_id);

		$props = MtHttp::get('prop/' . $app_id);

		return view('segment/create', [
			'actions' => $actions,
			'props' => $props
		]);
	}
}
