<?php
use Illuminate\Support\Facades\Input;

$is_new = isset($condition) ? false : true;

$condition = isset($condition) ? $condition : (object)[
				'select_type' => 'user',
				'type' => '',
				'select_type' => '',
				'f' => '',
				'field' => '',
				'fields' => [],
				'operator' => '',
				'operators' => [],
				'value' => '',
				'conditions' => [],
];
if ($is_new) {
	$condition->select_type = 'user';
}
$type_options = isset($type_options) ? $type_options : [];
$i_condition = isset($i_condition) ? $i_condition : 'i_condition_replace';
$f_behaviors = [
				(object)['code' => 'sum', 'name' => 'Sum'],
				(object)['code' => 'avg', 'name' => 'Avg'],
				(object)['code' => 'count', 'name' => 'Count']
];

$operators_default = [
(object)['code' => 'gt', 'name' => '>'],
(object)['code' => 'gte', 'name' => '>='],
(object)['code' => 'eq', 'name' => '='],
(object)['code' => 'lt', 'name' => '<'],
(object)['code' => 'lte', 'name' => '<='],
//(object)['code' => '>', 'name' => 'greater than'],
//(object)['code' => '>=', 'name' => 'greater or equal'],
//(object)['code' => '=', 'name' => 'equal'],
//(object)['code' => '<', 'name' => 'less than'],
//(object)['code' => '<=', 'name' => 'less or equal'],
];
;
?>


<div class="condition-item row" data-name="condition-item" data-i-condition="{{$i_condition}}">
    <div class="col-md-12" style="padding-top: 0; padding-bottom: 0">
        <div class="row">
            <!--<label class="col-md-2" style="margin-top: 10px">List top</label>-->
            <div class="col-md-2" data-name="condition-item-type">
                <input class="row hidden" type="hidden" name="Segment[{{$i_condition}}][select_type]" value="{{$condition->select_type}}">
                <select class="form-control" name="Segment[{{$i_condition}}][type]" onchange="typeChange(this)"  value = "{{$condition->type}}">
                    <?php
                    foreach ($type_options as $type_option):
                        ?>
                        <option value="<?= $type_option->value ?>"
                        <?= $type_option->value == $condition->type ? 'selected=""' : '' ?>
                        <?= $type_option->value == '[disabled]' ? 'disabled' : '' ?>
                                data-select-type="<?= $type_option->select_type ?>">
                                    <?= $type_option->name ?>
                        </option>
                        <?php
                    endforeach;
                        ?>
                </select>
            </div>
            <div class="col-md-2"  data-name="label_for_did_action" style="display:none">
                <h6 style="margin-bottom:0px">With number of time</h6>
            </div>
            <div class="col-md-2" <?= $condition->select_type == 'user' ? 'style="display: none"' : 'style="display: none"' ?>>
                <select class="form-control" id="" name="Segment[<?= $i_condition ?>][f]" value="<?= $condition->f ?>">
                    <?php
                    foreach ($f_behaviors as $f_behavior):
                        ?>
                        <option value="<?= $f_behavior->code ?>"
                                <?= $f_behavior->code == $condition->f ? 'selected=""' : '' ?> >
                                    <?= $f_behavior->name ?>
                        </option>
                        <?php
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2" <?= $condition->select_type == 'user' ? 'style="display: none"' : 'style="display: none"' ?>>
                <select class="form-control" id=""  name="Segment[<?= $i_condition ?>][field]" value="<?= $condition->field ?>"
                        onchange="changeField(this)">
                            <?php
                            foreach ($condition->fields as $c_field):
                                ?>
                        <option value="<?= $c_field->pcode ?>" 
                                <?= $c_field->pcode == $condition->field ? 'selected=""' : '' ?>><?= $c_field->pname ?></option>
                        <?php
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2" <?= $condition->select_type == 'user'? 'style="display: none"' : ''  ?>>
                <h6 style="margin-bottom:0px" id="label_for_did_action"><?= $condition->select_type == 'user'? '' : 'With number of time'  ?></h6>
            </div>
            <div class="col-md-2" data-name="condition-item-operator">
                <select class="form-control" id="" name="Segment[<?= $i_condition ?>][operator]" onchange="operatorChange(this)">
                    <?php
                    if (property_exists($condition, 'operators')) {
                        $operators = $condition->operators ? $condition->operators : $operators_default;
                    }
                    foreach ($operators as $operator):
                        ?>
                        <option value="<?= $operator->code ?>" <?= $operator->code == $condition->operator ? 'selected=""' : '' ?>>
                            <?= $operator->name ?>
                        </option>
                        <?php
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2" data-name="condition-item-value">
                <input type="text" class="form-control " name="Segment[<?= $i_condition ?>][value]"
                       value="<?= Input::old('Segment[' . $i_condition . '][value]', $condition->value) ?>"/>
            </div>
            <div class="col-md-1 col-add-condition add"
                 data-name="add-condition" <?= $condition->select_type == 'user' ? 'style="display: none; padding-left: 6px !important; width: 34px;"' : 'style="padding-left: 6px !important; width: 34px;"' ?>>
                <i class="fa fa-plus fa-2" aria-hidden="true" onclick="addCondition(this)"></i>
            </div>
            <div class="col-md-1 col-add-filter" style="padding-top: 6px; text-align: left;">
                <a class="button" href="javascript:void(0);" style=""
                   onclick="addFilter(this)" data-name="a-add-filter"><span class="icon icon3"></span></a>
                <a class="button" href="javascript:void(0);" style=""
                   onclick="deleteFilter(this)" data-name="a-delete-filter"><span class="icon icon58"></span> </a>
            </div>
        </div>
        <div class="row">
            <div class="condition-sub-group col-md-12" style="padding-bottom: 0" data-name="condition-sub-group" data-i-condition-sub-max="{{count($condition->conditions)}}">
                <?php
                $i_condition_sub = 0;
                if ($condition->conditions && is_array($condition->conditions)):
                    foreach ($condition->conditions as $c_condition):
                        ?>
                        @include('segment.partials.condition-sub-item', [
                        'condition' => $condition,
                        'c_condition' => $c_condition,
                        'i_condition' => $i_condition,
                        'i_condition_sub' => $i_condition_sub,
                        ])
                        <?php
                        $i_condition_sub++;
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>
</div>