<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ApplySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Applies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apply-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Apply', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'email:email',
            'phone',
            'dob',
            // 'file',
            // 'github',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
