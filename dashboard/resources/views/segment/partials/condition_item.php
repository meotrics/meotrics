<?php
$condition;
$type_options;
$operators;
$fields;
?>
<?php
if($condition->select_type == 'user'):
?>
<div class="col-md-4">
    <select class="form-control" id="" name="Segment[type][]">
        <?php
        foreach ($type_options as $type_option):
        ?>
        <option value="<?=$type_option->value?>" 
            <?= $type_option->value == $condition->type ? 'selected=""' : '' ?> 
            <?= $type_option->value == '[disabled]' ? 'disabled' : ''?> >
            <?=$type_option->name?>
        </option>
        <?php
        endforeach;
        ?>
    </select>
</div>
<div class="col-md-4">
    <select class="form-control" id="" name="Segment[operator][]">
        <?php
        foreach ($operators as $operator):
        ?>
        <option value="<?=$operator->code?>" <?= $operator->code == $condition->operator ? 'selected=""' : '' ?>>
            <?=$operator->name?>
        </option>
        <?php
        endforeach;
        ?>
    </select>
</div>
<div class="col-md-2">
    <input type="text" class="form-control " name="Segment[value][]" required="" value="<?=$condition->value?>"/>
</div>
<div class="col-md-2 col-add-filter">
    <a class="" href="javascript:void(0);" onclick="">+ Add filter</a>
</div>
<?php
else:
?>
<div class="col-md-2">
    <select class="form-control" id="" name="Segment[type][]">
        <?php
        foreach ($type_options as $type_option):
        ?>
        <option value="<?=$type_option->value?>" 
            <?= $type_option->value == $condition->type ? 'selected=""' : '' ?> 
            <?= $type_option->value == '[disabled]' ? 'disabled' : ''?> >
            <?=$type_option->name?>
        </option>
        <?php
        endforeach;
        ?>
    </select>
</div>
<div class="col-md-2">
    <select class="form-control" id="" name="Segment[f][]">
        <option value="sum">Sum</option>
        <option value="avg">Avg</option>
        <option value="count">count</option>
    </select>
</div>
<div class="col-md-2">
    <select class="form-control" id="" name="Segment[field][]">
        <?php
        foreach($fields as $field):
        ?>
        <option value="<?=$field->pcode?>"><?=$field->pname?></option>
        <?php
        endforeach;
        ?>
    </select>
</div>
<div class="col-md-2">
    <select class="form-control" id="" name="Segment[operator][]">
        <?php
        foreach ($operators as $operator):
        ?>
        <option value="<?=$operator->code?>" <?= $operator->code == $condition->operator ? 'selected=""' : '' ?>>
            <?=$operator->name?>
        </option>
        <?php
        endforeach;
        ?>
    </select>
</div>
<div class="col-md-2">
    <input type="text" class="form-control " name="Segment[value][]" required="" value="<?=$condition->value?>"/>
</div>
<div class="col-md-2 col-add-filter">
    <a class="" href="javascript:void(0);" onclick="">+ Add filter</a>
</div>
<?php
endif;
?>