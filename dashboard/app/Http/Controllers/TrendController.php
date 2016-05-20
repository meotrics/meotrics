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

//use function redirect;
//use function response;
//use function view;

class TrendController extends Controller
{
	private $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
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

	public function getList()
	{
		return json_encode(MtHttp::get('trend/' . '1'));
	}

	public function getDelete()
	{

	}

	public function postCurrenttime(Request $request, $appid)
	{
		$response = new Response();
		$st = $this->request->input('startTime', null);
		$et = $this->request->input('endTime', null);
		$response->withCookie(cookie('currenttrendstarttime', $st, 2147483647, '/trend/' . $appid . '/'));
		$response->withCookie(cookie('currenttrendendtime', $et, 2147483647, '/trend/' . $appid . '/'));
		return $response;
	}

	public function postCurrentsegment(Request $request, $appid)
	{
		$response = new Response();
		$segmentid = $this->request->input('segmentid', '');
		return $response->withCookie(cookie('currentsegmentid', $segmentid, 2147483647, '/trend/' . $appid . '/'));
	}

	public function postCurrenttrend(Request $request, $appid, $trendid)
	{
		$response = new Response();
		return $response->withCookie(cookie('currenttrendid', $trendid, 2147483647, '/'. $appid . '/'));
	}

	public function getIndex(Request $request, $app_id, $trendid = null)
	{
		$actiontypes = MtHttp::get('actiontype/' . $app_id);

		$segid = $this->request->cookie('currentsegmentid');
		$st = $this->request->cookie('currenttrendstarttime');
		$et = $this->request->cookie('currenttrendendtime');

		$trends = MtHttp::get('trend/' . $app_id);

		if($trendid !== null) {
			$tre = MtHttp::get('trend/' . $app_id . '/' . $trendid);
			if($tre == null)
				return view('trend/notfound');
		}
		
		
		if ($trends) {
			$queryurl = 'trend/query/' . $app_id;
			if ($trendid!= null) {
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
			'segmentid' => $this->request->cookie('currentsegmentid'),
			'startTime' => $this->request->cookie('currenttrendstarttime'),
			'endTime' => $this->request->cookie('currenttrendendtime'),
			'segments' => $segments,
			'trendid' => $trendid,
			'types' => json_encode($actiontypes),
			'trends' => $trends,
			'outputs' => $outputs,
//                'actiontypes' => $actiontypes,
		]);
	}

	public function getQuery(Request $request, $app_id)
	{
		$result = ['success' => false];
		if ($request->input('_id') != null) {
			$trendid = $request->input('_id');
			$outputs = MtHttp::get('trend/query/' . $app_id . '/' . $trendid);
			$result['success'] = true;
			$result['outputs'] = $outputs;
		} else {
//			$data = array(
//				'typeid' => $request->input('typeid'),
//				'object' => $request->input('object'),
//				'operation' => $request->input('operation'),
//				'param' => $request->input('param'),
//				'order' => intval($request->input('order')),
//				'name' => 'Draft',
//				'_isDraft' => true
//			);
//			$trendid = MtHttp::post('trend/1', $data);
			$result['success'] = false;
		}
		return $result;
	}

	public function getCreate(Request $request, $app_id)
	{
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
				$trendid = MtHttp::put('trend/' . $app_id . '/' . $data_post['_id'], $data);
			} else {
				$trendid = MtHttp::post('trend/' . $app_id, $data);
			}
			return redirect('trend/' . $app_id . '/' . $trendid);
		}
		return false;
	}

	public function getMeotrics($app_id, $action_id)
	{
		$result = ['success' => false];
		/*** get meotrics by appid and actionid ***/

		$meotrics = [];
		for ($i = 0; $i < 5; $i++) {
			$meotric = new stdClass();
			$meotric->name = "Number of pageview " . $i;
			$meotric->operation = TrendEnum::DEFAULT_OPERATION;
			$meotric->param = TrendEnum::DEFAULT_PARAM;
			$meotrics[] = $meotric;
		}
		$result['success'] = true;
		$result['meotrics'] = $meotrics;
		return response()->json($result);
	}

	public function getHtmloutputs(Request $request, $app_id)
	{
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
		$result = ['success' => false];
		$trend = $this->loadTrend($app_id, $id);
		$result = MtHttp::delete('trend/' . $app_id . '/' . $id, null);
		//check result overhere

		$result['success'] = true;
		return $result;
	}
}
