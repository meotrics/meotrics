<?php namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Util\MtHttp;
use App\Util\Access;

class TypeController extends Controller
{
    public function __construct(Request $request) {
		$this->middleware('auth');
    }

	public function index(Request $request, $appid)
	{
        if(Access::can_view($request->user()->id, $appid) == false) abort(500,'Permission Denied');
		$result = MtHttp::get('actiontype/' . $appid);
		return view('actiontype/index', [
			'appcode' => $appid,
			'actiontypes' => $result
		]);
	}

	public function show(Request $request, $appid, $id)
	{
        if(Access::can_view($request->user()->id, $appid) == false) abort(500,'Permission Denied');
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
        if(Access::can_editStruct($request->user()->id, $appid) == false) abort(500,'Permission Denied');
		$p = $request->all();
		$p['fields'] = array();
		for($i = 0; $i < count($p['pcodes']); $i++){
			$pn = $p['pnames'][$i];
			$pc = $p['pcodes'][$i];
			if($pn && $pc && $pn != '' && $pc != '' && substr($pc, 0, 1) != "_"){
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
 if(Access::can_editStruct($request->user()->id, $appcode) == false) abort(500,'Permission Denied');
		return view('actiontype/create', ['appcode' => $appcode]);
	}

	public function store(Request $request, $appcode){
 if(Access::can_editStruct($request->user()->id, $appcode) == false) abort(500,'Permission Denied');
		$p = $request->all();
		$p['fields'] = array();
		for($i = 0; $i < count($p['pcodes']); $i++){
			$pn = $p['pnames'][$i];
			$pc = $p['pcodes'][$i];

			if($pn && $pc && $pn != '' && $pc != '' && substr($pc, 0, 1) != "_"){
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
 if(Access::can_editStruct($request->user()->id, $appcode) == false) abort(500,'Permission Denied');
		$result = MtHttp::delete('actiontype/' . $appcode . '/' . $id, array());
		return redirect("actiontype/$appcode");
	}
}
