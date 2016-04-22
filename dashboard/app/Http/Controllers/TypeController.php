<?php namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Util\MtHttp;

class TypeController extends Controller
{
	public function __construct(Request $request) {
    $this->request = $request;
		$this->middleware('auth');
  }

	public function index(Request $request)
	{
		$app_id = \Auth::user()->id;
		$result = MtHttp::get('actiontype/' . $app_id);
		return view('actiontype/index', [
			'actiontypes' => $result
		]);
	}

	public function show($id)
	{
		$app_id = \Auth::user()->id;
		$result = MtHttp::get('actiontype/' . $app_id . '/' . $id);
		if($result){
			return view('actiontype/edit', [
				'type' => $result
			]);
		} else {
			abort(404);
		}
	}
	public function update($id)
	{
		$app_id = \Auth::user()->id;
		$p = $this->request->all();
		$p['fields'] = array();
		for($i = 0; $i < count($p['pcodes']); $i++){
			$pn = $p['pnames'][$i];
			$pc = $p['pcodes'][$i];
			if($pn && $pc && $pn != '' && $pc != ''){
				array_push($p['fields'], array('pname' => $pn, 'pcode' => $pc));
			} else {
				continue;
			}
		}
		unset($p['pcodes']);
		unset($p['pnames']);
		unset($p['_method']);
		MtHttp::put('actiontype/' . $app_id . '/' . $id, $p);
		return Redirect::action('TypeController@show', $id)->with('success', 'Action type saved !');;
	}

	public function create()
	{	
		return view('actiontype/create');
	}

	public function store(){
		$app_id = \Auth::user()->id;
		$p = $this->request->all();
		$p['fields'] = array();
		for($i = 0; $i < count($p['pcodes']); $i++){
			$pn = $p['pnames'][$i];
			$pc = $p['pcodes'][$i];
			if($pn && $pc && $pn != '' && $pc != ''){
				array_push($p['fields'], array('pname' => $pn, 'pcode' => $pc));
			} else {
				continue;
			}
		}
		unset($p['pcodes']);
		unset($p['pnames']);
		unset($p['_method']);
		$result = MtHttp::post('actiontype/' . $app_id, $p);
		return Redirect::action('TypeController@index');
	}

	public function destroy($id)
	{
		$app_id = \Auth::user()->id;
		$result = MtHttp::delete('actiontype/' . $app_id . '/' . $id, array());
		return Redirect::action('TypeController@index');
	}

}
