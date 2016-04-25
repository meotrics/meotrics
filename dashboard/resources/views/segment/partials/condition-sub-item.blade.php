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
$condtion_sub_operators = App\Enum\SegmentEnum::conditionSubOperators();
?>
<div class="row" data-name="condition-sub-item">
	<div class="col-md-2 col-md-offset-1">
		<select class="form-control "
		        name="Segment[{{ $i_condition }}][conditions][{{ $i_condition_sub }}][cs_field]"
		        value="{{ $condition->field }}">

			@if ($condition->fields)
				@foreach ($condition->fields as $c_field)
					<option value="{{ $c_field->pcode }}" {{ $c_condition->cs_field == $c_field->pcode ? 'selected=""' : '' }}>{{$c_field->pname }}</option>
				@endforeach
			@else
			@endif
		</select>
	</div>
	<div class="col-md-2">
		<select class="form-control" name="Segment[{{ $i_condition}}][conditions][{{$i_condition_sub}}][cs_operator]">
			@foreach ($condtion_sub_operators as $cso_key => $cso_value)
				<option value="{{ $cso_key }}" <?= $c_condition->cs_operator == $cso_key ? 'selected=""' : '' ?>>
					{{ $cso_value}}
				</option>
			@endforeach
		</select>
	</div>
	<div class="col-md-2">
		<input type="text" class="form-control "
		       name="Segment[{{ $i_condition }}][conditions][{{ $i_condition_sub }}][cs_value]"
		       value="{{ $c_condition->cs_value }}"/>
	</div>
	<div class="col-md-1 col-add-filter add">
		<i class="fa fa-minus fa-2" aria-hidden="true" onclick="deleteCondition(this)"></i>
		<i class="fa fa-plus fa-2" aria-hidden="true" onclick="addCondition(this)"></i>
	</div>
</div>
