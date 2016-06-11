<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Apply */
$this->title = 'Create Apply';
//$this->params['breadcrumbs'][] = ['label' => 'Applies', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div style="width: 100%">
<div class="apply-create">
    <div style="text-align: center">
        <h1><?= Html::encode("Apply Now!") ?></h1>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</div>