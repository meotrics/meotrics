<?php

namespace App\Http\Controllers;

use App\Util\MtHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Util\Access;
use Auth;
class SegmentController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
		$this->reftypemap = ["Unknown","Paid Search", "Organice Search", "Social Network", "Referral", "Email", "Direct"];
	}

	public function getExecute(Request $request, $appcode, $segid)
	{
        if(Access::can_view($request->user()->id, $appcode) == false) abort(500, "Permission Denied");
		$model = MtHttp::get('segment/' . $appcode . '/' . $segid);
		return json_encode($model);
	}

	public function getIndex(Request $request, $appcode, $segid = null)
	{
        if(Access::can_view($request->user()->id, $appcode) == false) abort(500, "Permission Denied");
		$props = MtHttp::get('prop/' . $appcode);
		$segments = MtHttp::get('segment/' . $appcode);

		//check if segment exist
		if ($segid !== null) {
			$seg = MtHttp::get('segment/' . $appcode . '/' . $segid);
			if ($seg == null)
				return view('segment/notfound');
		}
		return view('segment/index', [
			'props' => $props,
			'segments' => $segments,
			'segmentid' => $segid
		]);
	}

	public function getCreate(Request $request, $app_id)
	{
        if(Access::can_editReport($request->user()->id, $app_id) == false) abort(500, "Permission Denied");
		$actions = MtHttp::get('actiontype/' . $app_id);
		$props = MtHttp::get('prop/' . $app_id);
		$prop_first = isset($props[0]) ? $props[0] : (object)[
			'name' => '',
			'code' => '',
			'operators' => '',
		];
		$operators = $prop_first->operators ? $prop_first->operators : [(object)[
			'name' => '',
			'code' => '',
		]];
		$operation_first = $operators[0];
		$conditions = [];
		$conditions[0] = (object)[
			'select_type' => 'user',
			'type' => $prop_first->code,
			'operator' => $operation_first->code,
			'value' => '',
			'f' => '',
			'field' => '',
			'fields' => [],
			'conditions' => [],
		];
		$html_sub_condition = View::make('segment/partials/condition-sub-item');
		$html_sub_condition = preg_replace("/\r|\n/", "", $html_sub_condition->render());
		$html_condition_item = View::make('segment/partials/condition_item');
		$html_condition_item = preg_replace("/\r|\n/", "", $html_condition_item->render());;
		return view('segment/create', [
			'segment' => (object)['_id' => '', 'name' => '', 'description' => ''],
			'actions' => $actions,
			'props' => $props,
			'conditions' => $conditions,
			'operators' => $operators,
			'html_sub_condition' => $html_sub_condition,
			'html_condition_item' => $html_condition_item,
		]);
	}

	public function getUpdate(Request $request, $app_id, $id)
	{
        if(Access::can_editReport($request->user()->id, $app_id) == false) abort(500, "Permission Denied");
		$t = time();
		$segment = $this->loadModel($app_id, $id);
		$props = MtHttp::get('prop/' . $app_id);
		$actions = MtHttp::get('actiontype/' . $app_id);
		$tmp_conditions = $segment->condition ? $segment->condition : [];
		$conditions = [];

		foreach ($tmp_conditions as $tmp_condition) {
			if ($tmp_condition != 'and' && is_object($tmp_condition) && property_exists($tmp_condition, 'type')) {
				if ($tmp_condition->type == 'user') {
					$con_array = $tmp_condition->conditions ? $tmp_condition->conditions : [];
					$operators = [];
					foreach ($props as $prop) {
						if ($prop->code == (isset($con_array[0]) ? $con_array[0] : '')) {
							$operators = $prop->operators ? $prop->operators : [];
						}
					}
					for ($i_ca = 0; $i_ca <= (count($con_array) / 4); $i_ca++) {
						if (isset($con_array[$i_ca * 4]) && isset($con_array[$i_ca * 4 + 1]) && isset($con_array[$i_ca * 4 + 2])) {
							$conditions[] = (object)[
								'select_type' => 'user',
								'type' => isset($con_array[$i_ca * 4]) ? $con_array[$i_ca * 4] : '',
								'operator' => isset($con_array[$i_ca * 4 + 1]) ? $con_array[$i_ca * 4 + 1] : '',
								'value' => isset($con_array[$i_ca * 4 + 2]) ? $con_array[$i_ca * 4 + 2] : '',
								'f' => '',
								'field' => '',
								'fields' => [],
								'operators' => $operators,
								'conditions' => [],
							];
						}
					}
				} else {
					$fields = [];
					foreach ($actions as $action) {
						if ($action->codename == $tmp_condition->type) {
							$fields = $action->fields ? $action->fields : [];
						}
					}
					$conditions_sub = [];
					if (property_exists($tmp_condition, 'conditions') && is_array($tmp_condition->conditions)) {
						foreach ($tmp_condition->conditions as $tcc_key => $tcc_value) {
							if ($tcc_key % 4 == 0) {
								if (!isset($conditions_sub[$tcc_key / 4])) {
									$conditions_sub[$tcc_key / 4] = (object)[
										'cs_field' => '',
										'cs_operator' => '',
										'cs_value' => '',
									];
								}
								$tmp_sub = $conditions_sub[$tcc_key / 4];
								$tmp_sub->cs_field = $tcc_value;
								$conditions_sub[$tcc_key / 4] = $tmp_sub;
							} elseif ($tcc_key % 4 == 1) {
								$tmp_sub = $conditions_sub[$tcc_key / 4];
								$tmp_sub->cs_operator = $tcc_value;
								$conditions_sub[$tcc_key / 4] = $tmp_sub;
							} elseif ($tcc_key % 4 == 2) {
								$tmp_sub = $conditions_sub[$tcc_key / 4];
								$tmp_sub->cs_value = $tcc_value;
								$conditions_sub[$tcc_key / 4] = $tmp_sub;
							}
						}
					}
					$conditions[] = (object)[
						'select_type' => 'behavior',
						'type' => $tmp_condition->type ? $tmp_condition->type : '',
						'operator' => $tmp_condition->operator ? $tmp_condition->operator : '',
						'value' => $tmp_condition->value ? $tmp_condition->value : '',
						'f' => $tmp_condition->f ? $tmp_condition->f : '',
						'field' => $tmp_condition->field ? $tmp_condition->field : '',
						'fields' => $fields,
						'operators' => [
							(object)['code' => '>', 'name' => '>'],
							(object)['code' => '>=', 'name' => '>='],
							(object)['code' => '=', 'name' => '='],
							(object)['code' => '<', 'name' => '<'],
							(object)['code' => '<=', 'name' => '<='],
						],
						'conditions' => $conditions_sub,
					];
				}
			}
		}
		$html_sub_condition = View::make('segment/partials/condition-sub-item');
		$html_sub_condition = preg_replace("/\r|\n/", "", $html_sub_condition->render());

		$html_condition_item = View::make('segment/partials/condition_item');
		$html_condition_item = preg_replace("/\r|\n/", "", $html_condition_item->render());;
		return view('segment/create', [
			'segment' => $segment,
			'actions' => $actions,
			'props' => $props,
			'conditions' => $conditions,
			'operators' => [],
			'html_sub_condition' => $html_sub_condition,
			'html_condition_item' => $html_condition_item,
		]);
	}

	public function loadModel($app_id, $id)
	{
        if(Access::can_view($request->user()->id, $app_id) == false) abort(500, "Permission Denied");
		$model = MtHttp::get('segment/' . $app_id . '/' . $id);
		if ($model) {
			return $model;
		} else {
			abort(404, 'Segment not found');
		}
	}

	public function getSuggest(Request $request, $appid, $typeid, $field, $query)
	{
		$ret = MtHttp::get('suggest/' . $appid . '/' . $typeid . '/' . $field . '/' . $query);
		return $ret;
	}

	public function postWrite(Request $request, $appcode)
	{
        if(Access::can_editReport($request->user()->id, $appcode) == false) abort(500, "Permission Denied");
		if (isset($_POST['Segment']) && is_array($_POST['Segment']) && isset($_POST['name'])) {
			$query = [];
			$data_post = $_POST['Segment'];
			/*
			 * validate here
			 */
			$validator = Validator::make(
				[
					'name' => $_POST['name'],
					'conditions' => '',
				], [
				'name' => 'required',
				'conditions' => 'sometimes',
			], [
					'conditions.required' => 'At least one condition is completed',
				]
			);
			$validator->sometimes('conditions', 'required', function ($input) use ($data_post) {
				$result = false;
				foreach ($data_post as $condition) {
					if (!isset($condition['value'])) {
						$result = true;
					}
				}
				return $result;
			});
			if ($validator->fails()) {
				return redirect()->back()->withInput(Input::old())->withErrors($validator);
			}
			$user_query = (object)[
				'type' => 'user',
				'conditions' => [],
			];
			foreach ($data_post as $data) {
				if (isset($data['select_type']) && $data['select_type'] == 'user' && isset($data['value'])) {
					$user_conditions = [
						isset($data['type']) ? $data['type'] : '',
						isset($data['operator']) ? $data['operator'] : '',
						isset($data['value']) ? (is_numeric($data['value']) ? (int)$data['value'] : $data['value']) : '',
						'and',
					];
					$user_query->conditions = array_merge($user_query->conditions, $user_conditions);
				} elseif (isset($data['select_type']) && $data['select_type'] != 'user' && isset($data['value'])) {
					$conditions = [];
					if (isset($data['conditions']) && is_array($data['conditions'])) {
						foreach ($data['conditions'] as $c_value) {
							if (isset($c_value['cs_value']) && $c_value['cs_value']) {
								$conditions[] = isset($c_value['cs_field']) ? $c_value['cs_field'] : '';
								$conditions[] = isset($c_value['cs_operator']) ? $c_value['cs_operator'] : '';
								$conditions[] = isset($c_value['cs_value']) ? (is_numeric($c_value['cs_value']) ? (int)$c_value['cs_value'] : $c_value['cs_value']) : '';
								$conditions[] = "and";
							}
						}
						array_pop($conditions);
					}
					$query[] = (object)[
						'type' => isset($data['type']) ? $data['type'] : '',
						'f' => isset($data['f']) ? $data['f'] : '',
						'field' => isset($data['field']) ? $data['field'] : '',
						'operator' => isset($data['operator']) ? $data['operator'] : '',
						'value' => isset($data['value']) ? (is_numeric($data['value']) ? (int)$data['value'] : $data['value']) : '',
						'conditions' => $conditions,
					];
					$query[] = 'and';
				}
			}
			array_pop($user_query->conditions);
			$query[] = $user_query;

			$id = isset($_POST['id']) && $_POST['id'] ? $_POST['id'] : 0;

			$times = explode(" ", $_POST['timerange']);
			$startTime = $times[0];
			$endTime = $times[2];
			if (!$id) {
				$id = MtHttp::post('segment/' . $appcode, [
					'condition' => $query,
					'name' => $_POST['name'],
					'startTime' => $startTime,
					'endTime' => $endTime,
					'description' => isset($_POST['description']) ? $_POST['description'] : '',
				]);
			} else {
				$id = MtHttp::put('segment/' . $appcode . '/' . $id, [
					'condition' => $query,
					'name' => $_POST['name'],
					'startTime' => $startTime,
					'endTime' => $endTime,
					'description' => isset($_POST['description']) ? $_POST['description'] : '',
				]);
			}
		}
		return redirect('segment/' . $appcode . '/' . $id);
	}

	public function deleteRemove(Request $request, $app_id, $id)
	{
        if(Access::can_editReport($request->user()->id, $app_id) == false) abort(500, "Permission Denied");
		$result = ['success' => false];
		$segment = $this->loadModel($app_id, $id);
		$result = MtHttp::delete('segment/' . $app_id . '/' . $id, null);
		//check result overhere
		$result['success'] = true;
		return $result;
	}

	public function getChartonefield(Request $request, $app_id)
	{
if(Access::can_view($request->user()->id, $app_id) == false) abort(500, "Permission Denied");
		$result = ['success' => false];
		if ($request->input('segment_id') && $request->input('field')) {
			//echo 'segment/query1/' . $app_id . '/' . $request->input('segment_id') . '/' . $request->input('field');
			$tmp_charts = MtHttp::get('segment/query1/' . $app_id . '/' . $request->input('segment_id') . '/' . $request->input('field'));
			$convert_data = $this->convertData($tmp_charts, $request->input('field'));
			$result['labels'] = isset($convert_data['labels']) ? $convert_data['labels'] : [];
			$result['datasets'] = isset($convert_data['datasets']) ? $convert_data['datasets'] : [];
			$result['success'] = true;
		}
		return $result;
	}

	private $reftypemap;
	public function getCharttwofields(Request $request, $app_id)
	{
if(Access::can_view($request->user()->id, $app_id) == false) abort(500, "Permission Denied");
		$result = ['success' => false];
		if ($request->input('segment_id') && $request->input('field1') && $request->input('field2')) {
			$tmp_charts = MtHttp::get('segment/query2/' . $app_id . '/' . $request->input('segment_id') . '/' . $request->input('field2') . '/' . $request->input('field1'));
			$labels = [];
			$datasets = [];
			$datasets_labels = [];
			if ($tmp_charts && is_array($tmp_charts)) {
				$tmp_charts_first = $tmp_charts[0];
				if (isset($tmp_charts_first->key) && (is_string($tmp_charts_first->key) || $tmp_charts_first->key == null)) {
					foreach ($tmp_charts as $tmp_chart) {
						if (isset($tmp_chart->values)) {
							$convert_data = $this->convertData($tmp_chart->values, $request->input('field1'));
							$labels = isset($convert_data['labels']) ? $convert_data['labels'] : [];
							$datasets = array_merge($datasets, (isset($convert_data['datasets']) ? $convert_data['datasets'] : []));
							if(  $request->input('field2') === '_reftype'  )
								$datasets_labels[] = $this->reftypemap[ $tmp_chart->key];
								else
							$datasets_labels[] = $tmp_chart->key;
						}
					}
				} elseif (isset($tmp_charts_first->key) && is_array($tmp_charts_first->key)) {
					$tmp_datasets = [];
					foreach ($tmp_charts as $tmp_chart) {
						if (isset($tmp_chart->values)) {
							$convert_data = $this->convertData($tmp_chart->values, $request->input('field1'));
							$labels = isset($convert_data['labels']) ? $convert_data['labels'] : [];
							foreach ($tmp_chart->key as $tc_key => $tc_value) {
								$tmp_datasets[$tc_value][] = $convert_data['datasets'][0];
							}
						}
					}
					foreach ($tmp_datasets as $td_label => $td_value) {
						if(  $request->input('field2') === '_reftype'  )
							$datasets_labels[] = $this->reftypemap[ $td_label];
						else
							$datasets_labels[] = $td_label;


						$tmp_data = [];
						foreach ($td_value as $tdv_value) {
							foreach ($tdv_value as $tv_key => $tv_data) {
								$tmp_data[$tv_key] = isset($tmp_data[$tv_key]) ? $tmp_data[$tv_key] + $tv_data : $tv_data;
							}
						}
						$datasets[] = $tmp_data;
					}
				} else {
					foreach ($tmp_charts as $tmp_chart) {
						if (isset($tmp_chart->values)) {
							$convert_data = $this->convertData($tmp_chart->values, $request->input('field1'));
							$labels = isset($convert_data['labels']) ? $convert_data['labels'] : [];
							$datasets = array_merge($datasets, (isset($convert_data['datasets']) ? $convert_data['datasets'] : []));
							$tmp_label = isset($tmp_chart->key->from) ? ('from ' . $tmp_chart->key->from) : '';
							$tmp_label .= isset($tmp_chart->key->to) ? (' to ' . $tmp_chart->key->to) : '';
							if(  $request->input('field2') === '_reftype'  )
								$datasets_labels[] = $this->reftypemap[ $tmp_label];
							else
								$datasets_labels[] = $tmp_label;

						}
					}
				}
			}


//            foreach ($tmp_charts as $tmp_chart) {
//                if(property_exists($tmp_chart, 'values')){
//                    $convert_data = $this->convertData($tmp_chart->values);
//                    $labels = isset($convert_data['labels']) ? $convert_data['labels'] : [];
//                    $datasets = array_merge($datasets, (isset($convert_data['datasets']) ? $convert_data['datasets'] : []));
//                    $datasets_labels[] = $tmp_chart->key;
//                }
//            }
			$result['labels'] = $labels;
			$result['datasets'] = $datasets;
			$result['datasets_labels'] = $datasets_labels;
			$result['success'] = true;
		}
		return $result;
	}

	public function convertData($charts, $f)
	{
		$result = ['datasets' => [], 'labels' => []];
		$labels = [];
		$datasets = [];
		$tmp_data = [];
		if (is_array($charts) && $charts) {
			$tmp_charts_first = $charts[0];
			if (!isset($tmp_charts_first->key))
				$tmp_charts_first->key = "N/A";
			if (property_exists($tmp_charts_first, 'key') && is_string($tmp_charts_first->key)) {

				foreach ($charts as $tmp_chart) {

					if( $f === '_reftype'  )
						$labels[] = $this->reftypemap[ $tmp_chart->key];
					else
						$labels[] =$tmp_chart->key;

					$tmp_data[] = $tmp_chart->count;
				}

				$datasets[] = (object)[
					'data' => $tmp_data,
				];
			} elseif (property_exists($tmp_charts_first, 'key') && is_array($tmp_charts_first->key)) {
				$tmp_data = [];
				foreach ($charts as $tmp_chart) {
					foreach ($tmp_chart->key as $tmp_label) {
						if (!in_array($tmp_label, $labels)) {

								$labels[] = $tmp_label;
							$tmp_data[$tmp_label] = 0;
						}
						$tmp_data[$tmp_label] += (int)$tmp_chart->count;
					}
				}
				$chart_data = [];
				foreach ($labels as $label) {
					$chart_data[] = $tmp_data[$label];
				}
				$datasets[] = (object)[
					'data' => $chart_data,
				];

				if ($f === '_reftype') {
					$labels = [];foreach ($charts as $tmp_chart)
					foreach ($tmp_chart->key as $tmp_label)
						if (!in_array($tmp_label, $labels))
							$labels[] = $this->reftypemap[$tmp_label];
					}
			} else {
				foreach ($charts as $tmp_chart) {
					if (!isset($tmp_chart->key))
						$tmp_chart->key = "N/A";

					$tmp_label = property_exists($tmp_chart, 'key') && property_exists($tmp_chart->key, 'from') ? ('from ' . $tmp_chart->key->from) : '';
					$tmp_label .= property_exists($tmp_chart, 'key') && property_exists($tmp_chart->key, 'to') ? (' to ' . $tmp_chart->key->to) : '';

						$labels[] = trim($tmp_label);
					$tmp_data[] = $tmp_chart->count;
				}
				$datasets[] = (object)[
					'data' => $tmp_data,
				];

				if ($f === '_reftype') {
					$labels = [];
					var_dump("he");
					die;
					foreach ($tmp_chart->key as $tmp_label)
						if (!in_array($tmp_label, $labels))
							$labels[] = $this->reftypemap[ trim($tmp_label)];
				}

			}
		}
		$result['datasets'] = $datasets;
		$result['labels'] = $labels;
		return $result;
	}
}
