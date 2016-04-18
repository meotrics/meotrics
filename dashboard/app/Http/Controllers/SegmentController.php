<?php namespace App\Http\Controllers;

use App\Util\MtHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

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
                    'conditions' => [],
                ];
                
                $html_sub_condition = View::make('segment/partials/condition-sub-item');
                $html_sub_condition = preg_replace( "/\r|\n/", "", $html_sub_condition->render() );
                $html_condition_item = View::make('segment/partials/condition_item');
                $html_condition_item = preg_replace( "/\r|\n/", "", $html_condition_item->render() );;
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
                        'conditions' => [],
                    ];
                }
                else{
                    $fields = [];
                    foreach ($actions as $action) {
                        if($action->codename == $tmp_condition->type){
                            $fields = $action->fields ? $action->fields : [];
                        }
                    }
                    $conditions_sub = [];
                    if(property_exists($tmp_condition, 'conditions') && is_array($tmp_condition->conditions)){
                        foreach ($tmp_condition->conditions as $tcc_key => $tcc_value) {
                            if($tcc_key % 4 == 0){
                                if(!isset($conditions_sub[$tcc_key / 4])){
                                    $conditions_sub[$tcc_key / 4] = (object)[
                                        'cs_field' => '',
                                        'cs_operator' => '',
                                        'cs_value' => '',
                                    ];
                                }
                                $tmp_sub = $conditions_sub[$tcc_key / 4];
                                $tmp_sub->cs_field = $tcc_value;
                                $conditions_sub[$tcc_key / 4] = $tmp_sub;
                            }
                            elseif($tcc_key % 4 == 1){
                                $tmp_sub = $conditions_sub[$tcc_key / 4];
                                $tmp_sub->cs_operator = $tcc_value;
                                $conditions_sub[$tcc_key / 4] = $tmp_sub;
                            }
                            elseif($tcc_key % 4 == 2){
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
        $html_sub_condition = preg_replace( "/\r|\n/", "", $html_sub_condition->render() );
        
        $html_condition_item = View::make('segment/partials/condition_item');
        $html_condition_item = preg_replace( "/\r|\n/", "", $html_condition_item->render() );;
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
    
    public function postWrite(Request $request){
        if(isset($_POST['Segment']) && is_array($_POST['Segment']) && isset($_POST['name'])){
            $query = [];
            $data_post = $_POST['Segment'];
            /*
             * validate here
             */
            $validator = Validator::make(
                [
                    'name' => $_POST['name'],
                    'conditions' => '',
                ],
                [
                    'name' => 'required',
                    'conditions' => 'sometimes',
                ],
                [
                    'conditions.required' => 'At least one condition is completed',
                ]
            );
            $validator->sometimes('conditions', 'required', function($input) use($data_post){
                $result = false;
                foreach ($data_post as $condition) {
                    if(!isset($condition['value']) || !$condition['value']){
                        $result = true;
                    }
                }
//                var_dump($data_post);
//                var_dump($result); exit;
                return $result;
            });
            if ($validator->fails()){
                return redirect()->back()->withInput(Input::old())->withErrors($validator);
            }
            $user_query = (object)[
                'type' => 'user',
                'conditions' => [],
            ];
            foreach ($data_post as $data) {
                if(isset($data['select_type']) && $data['select_type'] == 'user' 
                        && isset($data['value']) && $data['value']){
                    $user_conditions = [
                        isset($data['type']) ? $data['type'] : '',
                        isset($data['operator']) ? $data['operator'] : '',
                        isset($data['value']) ? ((int)$data['value'] ? (int)$data['value'] : $data['value']) : '',
                        'and',
                    ];
                    $user_query->conditions = array_merge($user_query->conditions, $user_conditions);
                }
                elseif(isset($data['select_type']) && $data['select_type'] != 'user'
                        && isset($data['value']) && $data['value']){
                    $conditions = [];
                    if(isset($data['conditions']) && is_array($data['conditions'])){
                        foreach ($data['conditions'] as $c_value) {
                            if(isset($c_value['cs_value']) && $c_value['cs_value']){
                                $conditions[] = isset($c_value['cs_field']) ? $c_value['cs_field'] : '';
                                $conditions[] = isset($c_value['cs_operator']) ? $c_value['cs_operator'] : '';
                                $conditions[] = isset($c_value['cs_value']) ? ((int)$c_value['cs_value'] ? (int)$c_value['cs_value'] : $c_value['cs_value']) : '';
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
                        'value' => isset($data['value']) ? ((int)$data['value'] ? (int)$data['value'] : $data['value']) : '',
                        'conditions' => $conditions,
                        
                    ];
                    $query[] = 'and';
                }
            }
            array_pop($user_query->conditions);
            $query[] = $user_query;
            
            $app_id = \Auth::user()->id;
            $id = isset($_POST['id']) && $_POST['id'] ? $_POST['id'] : 0;
            if (!$id) {
                $id_new = MtHttp::post('segment/' . $app_id, [
                    'condition' => $query, 
                    'name' => $_POST['name'],
                    'description' => isset($_POST['description']) ? $_POST['description'] : '', 
                ]);
            }
            else{
                $id = MtHttp::put('segment/' . $app_id .'/' .$id, [
                    'condition' => $query, 
                    'name' => $_POST['name'],
                    'description' => isset($_POST['description']) ? $_POST['description'] : '', 
                ]);
            }
        }
        return redirect('segment');
    }
    
    public function deleteRemove($id){
        $result = ['success' => false];
        $app_id = \Auth::user()->id;
        $segment = $this->loadModel($id);
        $result = MtHttp::delete('segment/'.$app_id.'/' . $id, null);  
        //check result overhere
        $result['success'] = true;
        return $result;
    }
        
}
