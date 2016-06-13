<?php namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Util\MtHttp;

class TypeController extends Controller
{
	public function __construct(Request $request) {
		$this->middleware('auth');
  }

	public function index(Request $request, $appid)
	{
		$result = MtHttp::get('actiontype/' . $appid);
		return view('actiontype/index', [
			'appcode' => $appid,
			'actiontypes' => $result
		]);
	}

	public function show(Request $request, $appid, $id)
	{
		$result = MtHttp::get('actiontype/' . $appid . '/' . $id);
		if($result){
			return view('actiontype/edit', [
				'appcode' => $appid,
				'type' => $result
			]);
		} else {
			abort(404);
		}
	}
	
	public function update(Request $request, $appid, $id)
	{
		$p = $request->all();
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
		MtHttp::put('actiontype/' . $appid . '/' . $id, $p);
		return redirect("actiontype/$appid");
	}

	public function getcreate(Request $request, $appcode)
	{	
		return view('actiontype/create', ['appcode' => $appcode]);
	}

	public function store(Request $request, $appcode){
		$p = $request->all();
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
		$result = MtHttp::post('actiontype/' . $appcode, $p);
		return redirect("actiontype/$appcode");
	}

	public function destroy(Request $request, $appcode, $id)
	{
		$result = MtHttp::delete('actiontype/' . $appcode . '/' . $id, array());
		return redirect("actiontype/$appcode");
	}
}
