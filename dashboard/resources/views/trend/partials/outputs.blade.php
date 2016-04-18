<?php
use App\Util\MtHttp;

$app_id = \Auth::user()->id;
$actiontypes = MtHttp::get('actiontype/' . $app_id);
$list_fields = [];
if($actiontypes){
    foreach ($actiontypes as $actiontype) {
        if(property_exists($actiontype, 'codename') && property_exists($actiontype, 'fields')
                && is_array($actiontype->fields)){
            foreach ($actiontype->fields as $a_field) {
                $list_fields[$a_field->pcode] = $a_field->pname;
            }
        }
    }
}
?>
<table class="table table-hover table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Result</th>
            <?php
            if($list_fields):
            foreach ($list_fields as $pcode => $pname):
            ?>
            <th><?= $pname ?></th>
            <?php
            endforeach;
            endif;
            ?>
        </tr>
    </thead>
    <tbody>
        @if($outputs)
            <?php
            foreach($outputs as $output):
                $temp_value = $output->temp;
            ?>
            <tr>
                <td>{{$output->_id}}</td>
                <td>{{$output->result}}</td>
                <?php
                if($list_fields):
                foreach ($list_fields as $pcode => $pname):
                ?>
                <td><?= property_exists($temp_value, $pcode) && $temp_value->$pcode ? $temp_value->$pcode : '-' ?></td>
                <?php
                endforeach;
                endif;
                ?>
            </tr>
            <?php
            endforeach;
            ?>
        @endif
    </tbody>
</table>