<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use common\assets\AdminLTEAsset;

AppAsset::register($this);

if (empty($this->params['bodyOptions'])) {
    $this->params['bodyOptions'] = [];
}

Html::addCssClass($this->params['bodyOptions'], ['hold-transition', AdminLTEAsset::$skin]);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<?= Html::beginTag('body', $this->params['bodyOptions']) ?>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
<?= Html::endTag('body') ?>
</html>
<?php $this->endPage() ?>
