<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->context->layout = '@frontend/views/layouts/blank.php';
$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
Html::addCssClass($this->params['bodyOptions'], 'login-page');
?>
<div class="login-box">
    <div class="login-logo">
        <h1><strong><?= Yii::$app->name ?></strong></h1>
    </div>

    <div class="login-box-body">
        <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['type'=>'email']) ?>

        <?= $form->field($model, 'phone')->textInput(['type'=>'number']) ?>

        <?= $form->field($model, 'gender')->inline()->radioList(
            [
                \common\models\User::GENDER_MALE => 'Male',
                \common\models\User::GENDER_FEMALE=>'Female'
            ]) ?>

        <?= $form->field($model, 'company') ?>
        <?= $form->field($model, 'fb') ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
