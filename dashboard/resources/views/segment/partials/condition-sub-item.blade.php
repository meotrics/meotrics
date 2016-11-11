<?php
$condition = isset($condition) ? $condition : (object)[
				'fields' => [],
				'field' => '',
];
$c_condition = isset($c_condition) ? $c_condition : (object)[
				'cs_fields' => '',
				'cs_operator' => '',
				'cs_value' => '',
];
$i_condition = isset($i_condition) ? $i_condition : 'i_condition_replace';
$i_condition_sub = isset($i_condition_sub) ? $i_condition_sub : 'i_condition_sub_replace';

// Vitle - 21/7
$operator_behavior = array(
		'gt' => 'greater than',
		'gte' => 'greater or equal',
		'eq' => 'equal',
		'lt' => 'less than',
		'lte' => 'less or equal'
);

$operator_default = array(
		'con'=> 'Contain',
		'eq' => 'Equal',
		'start_with' => 'Start with',
		'end_with' => 'end_with',
		'ncon' => 'Not contain',

);

$condtion_sub_operators = $operator_default;
?>
@if ($condition->fields)
	@if($c_condition->cs_field == 'pid' || $c_condition->cs_field == 'cid' || $c_condition->cs_field == 'price' ||
            $c_condition->cs_field == 'quantity' || $c_condition->cs_field == '_ctime' || $c_condition->cs_field == 'oid' ||
            $c_condition->cs_field == 'level'  || $c_condition->cs_field == 'amount')
		@if($condtion_sub_operators = $operator_behavior)
		@endif
	@endif
@endif

<div class="row" data-name="condition-sub-item" data-i-condition-sub="{{ $i_condition_sub }}">
    <div class="col-md-2 col-md-offset-2" >
		<select class="form-control "
		        name="Segment[{{ $i_condition }}][conditions][{{ $i_condition_sub }}][cs_field]"
                        value="{{ $condition->field }}" onchange="changeSubField(this)">
			@if ($condition->fields)
				@foreach ($condition->fields as $c_field)
					<option value="{{ $c_field->pcode }}" {{ $c_condition->cs_field == $c_field->pcode ? 'selected=""' : '' }}>{{$c_field->pname }}</option>
				@endforeach
			@else
			@endif
		</select>
	</div>
	<div class="col-md-2" data-name="condition-sub-operator">
		<select class="form-control" 
                        name="Segment[{{ $i_condition}}][conditions][{{$i_condition_sub}}][cs_operator]"
                        onchange="operatorSubChange(this)">
			@foreach ($condtion_sub_operators as $cso_key => $cso_value)
				<option value="{{ $cso_key }}" <?= $c_condition->cs_operator == $cso_key ? 'selected=""' : '' ?>>
					{{ $cso_value}}
				</option>
			@endforeach
		</select>
	</div>
	<div class="col-md-2" data-name="condition-sub-value">
		<input type="text" class="form-control "
		       name="Segment[{{ $i_condition }}][conditions][{{ $i_condition_sub }}][cs_value]"
		       value="{{ $c_condition->cs_value }}"/>
		
	</div>
	<div class="col-md-1 col-add-filter add">
		<i class="fa fa-minus fa-2" aria-hidden="true" onclick="deleteCondition(this)"></i>
		<i class="fa fa-plus fa-2" aria-hidden="true" onclick="addCondition(this)"></i>
	</div>
</div>
