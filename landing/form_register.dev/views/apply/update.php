<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Apply */

$this->title = 'Update Apply: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Applies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="apply-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
