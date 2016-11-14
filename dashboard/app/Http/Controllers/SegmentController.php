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
        $this->reftypemap = ["Unknown", "Paid Search", "Organic Search", "Social Network", "Referral", "Email", "Direct"];
    }

    public function getExecute(Request $request, $appcode, $segid)
    {
        if (Access::can_view($request->user()->id, $appcode) == false) abort(500, "Permission Denied");
        $model = MtHttp::get('segment/' . $appcode . '/' . $segid);
        return json_encode($model);
    }

    public function getRefresh(Request $request, $appcode, $segid){
        if (Access::can_view($request->user()->id, $appcode) == false) abort(500, "Permission Denied");
        $model = MtHttp::get('segment/refresh/' . $appcode . '/' . $segid);
        return json_encode($model);
    }

    public function getIndex(Request $request, $appcode, $segid = null)
    {
        if (Access::can_view($request->user()->id, $appcode) == false) abort(500, "Permission Denied");
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
        if (Access::can_editReport($request->user()->id, $app_id) == false) abort(500, "Permission Denied");
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
        if (Access::can_editReport($request->user()->id, $app_id) == false) abort(500, "Permission Denied");
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

        if (Access::can_view(\Auth::user()->id, $app_id) == false) abort(500, "Permission Denied");
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
        if (Access::can_editReport($request->user()->id, $appcode) == false) abort(500, "Permission Denied");
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
//            $startTime = $times[0];
//            $endTime = $times[2];
            $startTime = '';
            $endTime = '';
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
        if (Access::can_editReport($request->user()->id, $app_id) == false) abort(500, "Permission Denied");
        $result = ['success' => false];
        $segment = $this->loadModel($app_id, $id);
        $result = MtHttp::delete('segment/' . $app_id . '/' . $id, null);
        //check result overhere
        $result['success'] = true;
        return $result;
    }

    public function getChartonefield(Request $request, $app_id)
    {
        if (Access::can_view($request->user()->id, $app_id) == false) abort(500, "Permission Denied");
        $result = ['success' => false];
        if ($request->input('segment_id') && $request->input('field')) {
            //echo 'segment/query1/' . $app_id . '/' . $request->input('segment_id') . '/' . $request->input('field');
            $tmp_charts = MtHttp::get('segment/query1/' . $app_id . '/' . $request->input('segment_id') . '/' . $request->input('field'));
            $convert_data = $this->convertData($tmp_charts, $request->input('field'));
            $result['labels'] = isset($convert_data['labels']) ? $convert_data['labels'] : [];
            $result['datasets'] = isset($convert_data['datasets']) ? $convert_data['datasets'] : [];
            /*
             * get list user
             */
            $result['users'] = MtHttp::get("segment/$app_id/" . $request->input('segment_id') . "/listUser/1/" . $request->input('field'));
            $result['success'] = true;
        }
        return $result;
    }

    private $reftypemap;

    public function getCharttwofields(Request $request, $app_id)
    {
        if (Access::can_view($request->user()->id, $app_id) == false) abort(500, "Permission Denied");
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
                            if ($request->input('field2') === '_reftype')
                                $datasets_labels[] = $this->reftypemap[$tmp_chart->key];
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
                        if ($request->input('field2') === '_reftype')
                            $datasets_labels[] = $this->reftypemap[$td_label];
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
                            if ($request->input('field2') === '_reftype')
                                $datasets_labels[] = $this->reftypemap[$tmp_label];
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
            /*
             * get list user
             */
            $result['users'] = MtHttp::get("segment/$app_id/" . $request->input('segment_id') . "/listUser/1/" . $request->input('field1') . "/" . $request->input('field2'));
            $result['success'] = true;
        }
        return $result;
    }

//    public function convertData($charts, $f)
//    {
//        $result = ['datasets' => [], 'labels' => []];
//        $labels = [];
//        $datasets = [];
//        $tmp_data = [];
//        if (is_array($charts) && $charts) {
//            $tmp_charts_first = $charts[0];
//            if (!isset($tmp_charts_first->key))
//                $tmp_charts_first->key = "N/A";
//            if (property_exists($tmp_charts_first, 'key') && is_string($tmp_charts_first->key)) {
//                foreach ($charts as $tmp_chart) {
//                    if ($f === '_reftype')
//                        $labels[] = $this->reftypemap[$tmp_chart->key];
//                    else
//                        $labels[] = $tmp_chart->key;
//
//                    $tmp_data[] = $tmp_chart->count;
//                }
//                if($f === "_ref"){
//                    // conver url
//                    foreach($charts as $tmp_chart){
//                        //remove https
//                        $tmp = str_replace("https://","",$tmp_chart->key);
//                        $tmp = str_replace("http://","",$tmp);
//                        $arr_url_have_uri = explode("/",$tmp);
//                        $server_name = $arr_url_have_uri[0];
//                        $tmp_chart->key = $server_name;
//                    }
//                    //return data and label
//                    $labels = [];
//                    $tmp_data = [];
//                    foreach($charts as $tmp_chart){
//                        if(!in_array($tmp_chart->key,$labels)){
//                            $labels[] = $tmp_chart->key;
//                            $tmp_data[$tmp_chart->key] = 0;
//                        }
//                        $tmp_data[$tmp_chart->key] += (int)$tmp_chart->count;
//                    }
//
//                }
//                $tmp = [];
//                foreach($labels as $label){
//                    $tmp[] = $tmp_data[$label];
//                }
//                $tmp_data = $tmp;
//                $datasets[] = (object)[
//                    'data' => $tmp_data,
//                ];
//            } elseif (property_exists($tmp_charts_first, 'key') && is_array($tmp_charts_first->key)) {
//                $tmp_data = [];
//                $tmp_name = []; // name ref
//                foreach ($charts as $tmp_chart) { // 1 object
//                    $tmp_chart_keys = isset($tmp_chart->key) && is_array($tmp_chart->key) ? $tmp_chart->key : []; // arr key in object
//                    foreach ($tmp_chart_keys as $tmp_label) {  // field in arr key
//                        if (!in_array($tmp_label, $labels)) {
//
//                            $labels[] = $tmp_label;
//                            if ($f === '_reftype')
//                                $tmp_name[] = $this->reftypemap[$tmp_label];
//                            $tmp_data[$tmp_label] = 0;
//                        }
//                        $tmp_data[$tmp_label] += (int)$tmp_chart->count;
//                    }
//                }
//                $chart_data = [];
//                // sap xep tu cao xuong thap
//                for($i=0; $i< count($labels); $i++){
//                    for($j = $i+1; $j<count($labels);$j++){
//                        $label = $labels[$i];
//                        $next_label = $labels[$j];
//                        if($tmp_data[$label] < $tmp_data[$next_label]){
//                            $tmp = $labels[$i];
//                            $labels[$i] = $labels[$j];
//                            $labels[$j] = $tmp;
//                        }
//                    }
//                }
//                $count = 0;
//                $labels_10 = [];
//                // only take 10 field
//                foreach ($labels as $label) {
//                    $count++;
//                    $chart_data[] = $tmp_data[$label];
//                    if($label == null){
//                        $label = "N/A";
//                    }
//                    $labels_10[] = $label;
//                    if($count == 10)
//                        break;
//                }
//                $labels = $labels_10;
//                $datasets[] = (object)[
//                    'data' => $chart_data,
//                ];
//
//                if ($f === '_reftype') {
//                    $labels = $tmp_name;
//                }
//            } else {
//                foreach ($charts as $tmp_chart) {
//                    if (!isset($tmp_chart->key))
//                        $tmp_chart->key = "N/A";
//
//                    $tmp_label = property_exists($tmp_chart, 'key') && property_exists($tmp_chart->key, 'from') ? ('from ' . $tmp_chart->key->from) : '';
//                    $tmp_label .= property_exists($tmp_chart, 'key') && property_exists($tmp_chart->key, 'to') ? (' to ' . $tmp_chart->key->to) : '';
//                    if($tmp_chart->key == null)
//                        $tmp_label = "N/A";
//                    $labels[] = trim($tmp_label);
//                    $tmp_data[] = $tmp_chart->count;
//                }
//                $datasets[] = (object)[
//                    'data' => $tmp_data,
//                ];
//
//                if ($f === '_reftype') {
//                    $labels = [];
//                    foreach ($tmp_chart->key as $tmp_label)
//                        if (!in_array($tmp_label, $labels))
//                            $labels[] = $this->reftypemap[trim($tmp_label)];
//                }
//
//            }
//        }
//        $result['datasets'] = $datasets;
//        $result['labels'] = $labels;
//        return $result;
//    }

    public function convertData($charts, $f)
    {
        $result = ['datasets' => [], 'labels' => []];
        $labels = [];
        $datasets = [];
        $tmp_data = [];
        if (is_array($charts) && $charts) {
            foreach ($charts as $tmp_chart) {
                if ($f === "_ref") {
                    // conver url
                    foreach ($charts as $tmp_charts) {
                        foreach($tmp_charts as $tmp_chart) {
                            //remove https
                            if(!isset($tmp_chart->key))
                                continue;
                            if ($tmp_chart->key === "") {
                                $tmp_chart->key = "None (direct)";
                            } else {
                                $tmp = str_replace("https://", "", $tmp_chart->key);
                                $tmp = str_replace("http://", "", $tmp);
                                $arr_url_have_uri = explode("/", $tmp);
                                $server_name = $arr_url_have_uri[0];
                                $tmp_chart->key = $server_name;
                            }
                        }
                    }
                }
                if ($f == "age") {
                    $tmp_label = property_exists($tmp_chart, 'key') && property_exists($tmp_chart->key, 'from') ? ('from ' . $tmp_chart->key->from) : '';
                    $tmp_label .= property_exists($tmp_chart, 'key') && property_exists($tmp_chart->key, 'to') ? (' to ' . $tmp_chart->key->to) : '';
                    if ($tmp_chart->key == null)
                        $tmp_label = "N/A";
                    $tmp_chart->key = trim($tmp_label);
                }
                if($f == "_lang"){
                    $lang = $tmp_chart->key;
                    if(is_array($lang)){
                        $lang = $tmp_chart->key[0];
                    }
                    $arr_lang = explode(",", $lang);
                    $tmp_chart->key = $arr_lang[0];
                }
                if($f == "_osver"){
                    foreach ($charts as $tmp_chart) {
                        $label = $tmp_chart->key;
                        if(is_array($label)){
                            $label = $label[0];
                        }
                        if($label == 'null.null')
                            $tmp_chart->key = null;
                    }
                }
                if($f == "_reftype"){
                    foreach ($charts as $tmp_chart) {
                        $label = $tmp_chart->key;
                        if(is_object($label)){
                            $arr_label = (array) $tmp_chart->key;
                            $label = implode("",$arr_label);
                        }
                        if($label == null ){
                            $tmp_chart->key = 0;
                        }

                    }
                }
            }
            foreach ($charts as $tmp_chart) { // 1 object
//                    $tmp_chart_keys = isset($tmp_chart->key) && is_array($tmp_chart->key) ? $tmp_chart->key : []; // arr key in object
                if (is_array($tmp_chart->key)) {
                    foreach ($tmp_chart->key as $tmp_label) {  // field in arr key
                        if ($tmp_label == null) {
                            $tmp_label = "N/A";
                        }
                        if (!in_array($tmp_label, $labels)) {
                            $labels[] = $tmp_label;
                            $tmp_data[$tmp_label] = 0;
                        }
                        $tmp_data[$tmp_label] += (int)$tmp_chart->count;
                    }
                } else {
                    $label = $tmp_chart->key;
                    if(is_object($label)){
                        $arr_label = (array) $tmp_chart->key;
                        $label = implode("",$arr_label);
                    }
                    if ($label === null) {
                        $label = "N/A";
                    }
                    if (!in_array($label, $labels)) {
                        $labels[] = $label;
                        $tmp_data[$label] = 0;
                    }
                    $tmp_data[$label] += (int)$tmp_chart->count;
                }
            }

            // sap xep tu cao xuong thap
            if($f != "age"){
                for ($i = 0; $i < count($labels); $i++) {
                    for ($j = $i + 1; $j < count($labels); $j++) {
                        $label = $labels[$i];
                        $next_label = $labels[$j];
                        if ($tmp_data[$label] < $tmp_data[$next_label]) {
                            $tmp = $labels[$i];
                            $labels[$i] = $labels[$j];
                            $labels[$j] = $tmp;
                        }
                    }
                }
            }
            $count = 0;
            $labels_10 = [];
            $chart_data = [];
            // only take 10 field
            foreach ($labels as $label) {
                $count++;
                $chart_data[] = $tmp_data[$label];
                $labels_10[] = $label;
                if ($count == 10)
                    break;
            }
            $labels = $labels_10;
            $datasets[] = (object)[
                'data' => $chart_data,
            ];
//            if ($f === '_reftype') {
//                $label_reftype = [];
//                foreach ($labels as $label) {
//                    if($label !== "N/A"){
//                        $label_reftype[] = $this->reftypemap[$label];
//                    }else{
//                        $label_reftype[] =  "N/A";
//                    }
//                }
//                $labels = $label_reftype;
//            }
        }
        $result['datasets'] = $datasets;
        $result['labels'] = $labels;
        return $result;
    }


    public function getUsersbyfield(Request $request, $app_id)
    {
        if (Access::can_view($request->user()->id, $app_id) == false)
            abort(500, "Permission Denied");
        $result = ['success' => false];
        if ($request->input('segment_id') && $request->input('field1') && $request->input('page')) {
            $get_url = "segment/$app_id/" . $request->input('segment_id') . "/listUser/" . $request->input('page') . "/" . $request->input('field1');
            if ($request->input('field2')) {
                $get_url .= "/" . $request->input('field2');
            }
            $result['users'] = MtHttp::get($get_url);
            $result['success'] = true;
        }
        if ($result != null) {
            if ($request->input('field1') === "_reftype") {
                foreach ($result['users'] as $user) {
                    $ref = [];
                    foreach ($user->_reftype as $item) {
                        $ref[] = $this->reftypemap[$item];
                    }
                    $user->_reftype = $ref;
                }
            }
        }
        if($request->input('field1') === "_osver"){
            foreach ($result['users'] as $user) {
                foreach($user->_osver as $key=>$item){
                    if($item == 'null.null'){
                        $user->_osver[$key] = "N/A";
                    }
                }
            }
        }
        if($request->input('field1') === "_lang"){
            foreach ($result['users'] as $user) {
                foreach($user->_lang as $key=>$item){
                    if($item == null){
                        $user->_lang[$key] = 'N/A';
                    }else{
                        $arr_item = explode(",",$item);
                        $user->_lang[$key] = $arr_item[0];
                    }
                }
            }
        }
        return $result;
    }
}
