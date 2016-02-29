<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->context->layout = '@frontend/views/layouts/blank.php';
$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
Html::addCssClass($this->params['bodyOptions'], 'login-page');
?>
<div class="login-box">
    <div class="login-logo">
        <h1><strong><?= Yii::$app->name ?></strong></h1>
    </div>

    <div class="login-box-body">
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= $form->field($model, 'rememberMe')->checkbox() ?>

        <div style="color:#999;margin:1em 0">
            If you forgot your password you can <?= Html::a('reset it', ['site/request-password-reset']) ?>.
        </div>

        <div class="form-group">
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <hr>
        <p style="color:#999;margin:1em 0"> If you don't have accout, please <?= Html::a('register',['site/signup']) ?></p>
    </div>
</div>
