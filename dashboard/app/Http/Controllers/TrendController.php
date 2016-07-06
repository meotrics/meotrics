<?php
namespace App\Http\Controllers;

use App\Enum\TrendEnum;
use App\Util\MtHttp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use stdClass;
use App\Util\Access;

class TrendController extends Controller
{
	public function __construct(Request $request)
	{
		$this->middleware('auth');
	}

	public function getSave(Request $request, $appid)
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

	public function postCurrenttime(Request $request, $appid)
	{
        if(Access::can_view($request->user()->id, $appid) == false) abort(500,'Permission Denied');
		$response = new Response();
		$st = $request->input('startTime', null);
		$et = $request->input('endTime', null);
		$response->withCookie(cookie('currenttrendstarttime', $st, 9147483, "/trend/$appid"));
		$response->withCookie(cookie('currenttrendendtime', $et, 9147483, "/trend/$appid"));
		return $response;
	}

	public function postCurrentsegment(Request $request, $appid)
	{
        if(Access::can_view($request->user()->id, $appid) == false) abort(500,'Permission Denied');
		$response = new Response();
		$segmentid = $request->input('segmentid', '');
		return $response->withCookie(cookie('currentsegmentid', $segmentid, 9147483, '/trend/' . $appid ));
	}

	public function postCurrenttrend(Request $request, $appid, $trendid)
	{
        if(Access::can_view($request->user()->id, $appid) == false) abort(500,'Permission Denied');
		$response = new Response();
		return $response->withCookie(cookie('currenttrendid', $trendid, 9147483, '/trend/' . $appid ));
	}

	public function getIndex(Request $request, $app_id, $trendid = null)
	{
        if(Access::can_view($request->user()->id, $app_id) == false) abort(500,'Permission Denied');
		$actiontypes = MtHttp::get('actiontype/' . $app_id);

		$segid = $request->cookie('currentsegmentid');
		$st = $request->cookie('currenttrendstarttime');
		$et = $request->cookie('currenttrendendtime');

		///var_dump($st);
		//die($st);
		$trends = MtHttp::get('trend/' . $app_id);

		if ($trendid !== null) {
			$tre = MtHttp::get('trend/' . $app_id . '/' . $trendid);
			if ($tre == null)
				return view('trend/notfound');
		}

		if ($trends) {
			$queryurl = 'trend/query/' . $app_id;
			if ($trendid != null) {
				$queryurl .= '/' . $trendid;
			} else {
				$trend = reset($trends);
				$queryurl .= '/' . $trend->_id;
			}

			if (isset($segid)) {
				$queryurl .= '/' . $segid;
			} else {
				$queryurl .= '/_';
			}

			if (isset($st)) {
				$pieces = explode("-", $st);
				$st = strtotime($pieces[1] . '/' . $pieces[2] . '/' . $pieces[0]);

				$pieces = explode("-", $et);
				$et  = strtotime($pieces[1] . '/' . $pieces[2] . '/' . $pieces[0]);
				$queryurl .= '/' . $st . '/' . $et;
			}

			try {
				$outputs = MtHttp::get($queryurl);
			} catch (\Exception $e) {
				$queryurl = 'trend/query/' . $app_id;
				$trend = reset($trends);
				$queryurl .= '/' . $trend->_id;
				if (isset($segid)) {
					$queryurl .= '/' . $segid;
				} else {
					$queryurl .= '/_';
				}

				if (isset($st)) {
					$queryurl .= '/' . $st . '/' . $et;
				}
				$outputs = MtHttp::get($queryurl);
			}
		} else {
			$outputs = [];
		}

		$segments = MtHttp::get('segment/' . $app_id);

		return view('trend/index', [
			'segmentid' => $request->cookie('currentsegmentid'),
			'starttime' => $request->cookie('currenttrendstarttime'),
			'endtime' => $request->cookie('currenttrendendtime'),
			'segments' => $segments,
			'trendid' => $trendid,
			'types' => json_encode($actiontypes),
			'trends' => $trends,
			'outputs' => $outputs,
//                'actiontypes' => $actiontypes,
		]);
	}

	public function getCreate(Request $request, $app_id)
	{
        if(Access::can_editReport($request->user()->id, $app_id) == false) abort(500,'Permission Denied');
		$actiontypes = MtHttp::get('actiontype/' . $app_id);
		$actiontype_first = $actiontypes && $actiontypes[0] ? $actiontypes[0] : (object)[
			'name' => '',
			'codename' => '',
			'fields' => [],
		];
		$trend = (object)[
			'_id' => '',
			'name' => '',
			'typeid' => $actiontype_first->codename,
			'object' => '',
			'operation' => '',
			'param' => '',
			'order' => TrendEnum::DEFAULT_ORDER,
		];
		return view('trend/c_u', [
			'app_id' => $app_id,
			'actiontypes' => $actiontypes,
			'trend' => $trend,
		]);
	}

	public function postWrite(Request $request, $app_id)
	{
        if(Access::can_editReport($request->user()->id, $app_id) == false) abort(500,'Permission Denied');
		if (isset($_POST['Trend'])) {

			// validate create data
			if (false == isset($data_post['_id'])) {
				$data_post = $_POST['Trend'];
				$validator = Validator::make(
					$data_post,
					[
						'typeid' => 'required',
						'object' => 'required',
						'operation' => 'required',
						'param' => 'required',
						'order' => 'required',
						'name' => 'required',
					]
				);
				if ($validator->fails()) {
					return redirect()->back()->withErrors($validator);
				}
			}

			$data = array(
				'_isDraft' => false
			);

			if (isset($data_post['typeid'])) $data['typeid'] = $data_post['typeid'];
			if (isset($data_post['object'])) $data['object'] = $data_post['object'];
			if (isset($data_post['operation'])) $data['operation'] = $data_post['operation'];
			if (isset($data_post['param'])) $data['param'] = $data_post['param'];
			if (isset($data_post['desc'])) $data['desc'] = $data_post['desc'];
			if (isset($data_post['order'])) $data['order'] = $data_post['order'];
			if (isset($data_post['name'])) $data['name'] = $data_post['name'];

			if (isset($data_post['_id']) && $data_post['_id']) {
				MtHttp::put('trend/' . $app_id . '/' . $data_post['_id'], $data);
				$trendid  = $data_post['_id'];
			} else {
				$trendid = MtHttp::post('trend/' . $app_id, $data);
			}
			return  redirect('trend/' . $app_id . '/' . $trendid);
		}
		return false;
	}

	public function getHtmloutputs(Request $request, $app_id)
	{
        if(Access::can_view($request->user()->id, $app_id) == false) abort(500,'Permission Denied');
		$result = ['success' => false];
		if ($request->input('_id') != null) {
			$trendid = $request->input('_id');
			$outputs = MtHttp::get('trend/query/' . $app_id . '/' . $trendid);
			$view = View::make('trend.partials.outputs', [
				'outputs' => $outputs,
			]);
			$contents = $view->render();
			$result['success'] = true;
			$result['html_outputs'] = $contents;
		} else {
			$result['success'] = false;
		}
		return $result;
	}

	public function getUpdate(Request $requrest, $app_id, $id)
	{
        if(Access::can_editReport($request->user()->id, $app_id) == false) abort(500,'Permission Denied');
		$trend = $this->loadTrend($app_id, $id);
		$actiontypes = MtHttp::get('actiontype/' . $app_id);
		$actiontype_first = $actiontypes && $actiontypes[0] ? $actiontypes[0] : (object)[
			'name' => '',
			'codename' => '',
			'fields' => [],
		];
		return view('trend/c_u', [
			'app_id' => $app_id,
			'actiontypes' => $actiontypes,
			'trend' => $trend,
		]);
	}

	public function loadTrend($app_id, $id)
	{
		$trend = MtHttp::get('trend/' . $app_id . '/' . $id);
		if ($trend) {
			return $trend;
		} else {
			App::abort(404, 'Trend not found');
		}
	}

	public function deleteRemove(Request $requrest, $app_id, $id)
	{
        if(Access::can_editReport($request->user()->id, $app_id) == false) abort(500,'Permission Denied');
		$result = ['success' => false];
		$trend = $this->loadTrend($app_id, $id);
		$result = MtHttp::delete('trend/' . $app_id . '/' . $id, null);
		//check result overhere

		$result['success'] = true;
		return $result;
	}
}
