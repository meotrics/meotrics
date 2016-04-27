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
				(object)['code' => '>', 'name' => '>'],
				(object)['code' => '>=', 'name' => '>='],
				(object)['code' => '=', 'name' => '='],
				(object)['code' => '<', 'name' => '<'],
				(object)['code' => '<=', 'name' => '<='],
];
?>



<div class="condition-item row" data-name="condition-item" data-i-condition="{{$i_condition}}">
    <div class="col-sm-12" style="padding-top: 0; padding-bottom: 0">
        <div class="row">
            <!--<label class="col-md-2" style="margin-top: 10px">List top</label>-->
            <div class="col-md-2">
                <input class="row hidden" type="hidden" name="Segment[{{$i_condition}}][select_type]" value="{{$condition->select_type}}">
                <select class="form-control" name="Segment[{{$i_condition}}][type]" onchange="typeChange(this)">
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
            <div class="col-md-2" <?= $condition->select_type == 'user' ? 'style="display: none"' : '' ?>>
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
            <div class="col-md-2" <?= $condition->select_type == 'user' ? 'style="display: none"' : '' ?>>
                <select class="form-control" id="" name="Segment[<?= $i_condition ?>][field]" value="<?= $condition->field ?>"
                        onchange="changeField(this)">
                            <?php
                            foreach ($condition->fields as $c_field):
                                ?>
                        <option value="<?= $c_field->pcode ?>"><?= $c_field->pname ?></option>
                        <?php
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control" id="" name="Segment[<?= $i_condition ?>][operator]">
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
            <div class="col-md-2">
                <input type="text" class="form-control " name="Segment[<?= $i_condition ?>][value]"
                       value="<?= Input::old('Segment[' . $i_condition . '][value]', $condition->value) ?>"/>
            </div>
            <div class="col-md-1 col-add-condition add"
                 data-name="add-condition" <?= $condition->select_type == 'user' ? 'style="display: none"' : '' ?>>
                <i class="fa fa-plus fa-2" aria-hidden="true" onclick="addCondition(this)"></i>
            </div>
            <div class="col-md-1 col-add-filter" style="padding-top: 10px;padding-bottom: 0">
                <a class="button left" href="javascript:void(0);" style="margin: 0; padding: 3px 0px; vertical-align: middle; width: 31px"
                   onclick="addFilter(this)" data-name="a-add-filter"><span class="icon icon3"></span></a>
                <a class="button right" href="javascript:void(0);" style="margin: 0;padding: 3px 0px; vertical-align: middle; width: 31px"
                   onclick="deleteFilter(this)" data-name="a-delete-filter"><span class="icon icon58"></span> </a>
            </div>
        </div>
        <div class="row">
            <div class="condition-sub-group col-sm-12" style="padding-bottom: 0" data-name="condition-sub-group" data-i-condition-sub-max="{{count($condition->conditions)}}">
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