<?php namespace App\Http\Controllers;

use App\Util\MtHttp;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
	public function getExecute(Request $request)
	{
		//get app id from cookie
		$appid = 1; //Cookie::get('appid');
		$query = $request->input('query');
		$id = $request->input('id');


		//Check if old segment
		if ($id == null || $id == -1) {

			$id = MtHttp::post('segment/' . $appid, ['query' => $query, 'name' => 'Draf']);
		}

		//run querry on field
		//MtHttp::get(':3000/segment/query')


		return json_encode([]);
	}

	public function getIndex()
	{
		$actions = MtHttp::get('actiontype/' . '1');

		$props = array(
			0 => array(
				'name' => 'gender',
				'dpname' => 'Gender'
			),
			1 => array(
				'name' => 'age',
				'dpname' => 'Age'
			),
			2 => array(
				'browser' => 'browser',
				'dpname' => 'Browser'
			)
		);
		$segments = MtHttp::get('segment/' . \Auth::user()->id);

		return view('segment/index', [
			'actions' => json_encode($actions),
			'props' => json_encode($props),
			'segments' => json_encode($segments)
		]);
	}
}
