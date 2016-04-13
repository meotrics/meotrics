<?php namespace App\Http\Controllers;

use App\Util\MtHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use function view;

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
                ];
//                var_dump($props[1]); exit;
		return view('segment/create', [
                    'actions' => $actions,
                    'props' => $props,
                    'conditions' => $conditions,
                    'operators' => $operators,
		]);
	}
        
    public function getUpdate($id){
        $segment = $this->loadModel($id);
        $app_id = \Auth::user()->id;
        $actions = MtHttp::get('actiontype/' . $app_id);
        $props = MtHttp::get('prop/' . $app_id);
        $tmp_conditions = $segment->condition ? $segment->condition : [];
        $conditions = [];
        foreach ($tmp_conditions as $tmp_condition) {
            if($tmp_condition != 'and' && is_object($tmp_condition) && property_exists($tmp_condition, 'type')){
                if($tmp_condition->type == 'user'){
                    $con_array = $tmp_condition->conditions ? $tmp_condition->conditions : [];
                    $operators = [];
                    foreach ($props as $prop) {
                        if($prop->code == (isset($con_array[0]) ? $con_array[0] : '')){
                            $operators = $prop->operators ? $prop->operators : [];
                        }
                    }
                    $conditions[] = (object)[
                        'select_type' => 'user',
                        'type' => isset($con_array[0]) ? $con_array[0] : '',
                        'operator' => isset($con_array[1]) ? $con_array[1] : '',
                        'value' => isset($con_array[2]) ? $con_array[2] : '',
                        'f' => '',
                        'field' => '',
                        'fields' => [],
                        'operators' => $operators,
                    ];
                }
                else{
                    $fields = [];
                    foreach ($actions as $action) {
                        if($action->codename == $tmp_condition->type){
                            $fields = $action->fields ? $action->fields : [];
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
                    ];
                }
            }
        }
        return view('segment/create', [
            'actions' => $actions,
            'props' => $props,
            'conditions' => $conditions,
            'operators' => [],
        ]);
    }
        
    public function loadModel($id){
        $app_id = \Auth::user()->id;
        $model = MtHttp::get('segment/'.$app_id.'/' . $id);  
        if($model){
            return $model;
        }
        else{
            App::abort(404, 'Segment not found');
        }
    }
        
}
