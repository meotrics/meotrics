<?php
echo "<head></head>";
use yii\helpers\Html;
use frontend\widgets\Menu;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

Html::addCssClass($this->params['bodyOptions'], 'sidebar-mini');

if (!isset($this->params['pageTitle'])) {
    $this->params['pageTitle'] = $this->title;
}

if (!isset($model)) {
    $model = new \yii\base\DynamicModel(['daterange']);
    $model->addRule(['daterange'],'date');
}

?>
<?php $this->beginContent('@frontend/views/layouts/blank.php'); ?>

    <div class="wrapper">

        <header class="main-header">

            <!-- Logo -->
            <a href="<?= \yii\helpers\Url::to(Yii::$app->homeUrl) ?>" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><?= Yii::$app->params['logoMini'] ?></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg" style="color: lightgray;"><?= Yii::$app->params['logoLg'] ?></span>
            </a>

            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>

                <div class="col-md-3 pull-right">
                    <div class="form-group">
                        <?php
                        use kartik\daterange\DateRangePicker;
                        use yii\widgets\ActiveForm;
                        $form = ActiveForm::begin();

                       echo DateRangePicker::widget([
    											'name'=>'date_range_3',
   												'value'=>'2015-10-19 AM  2015-11-03',
  												'convertFormat'=>true,
    											'pluginOptions'=>[
        										'opens'=>'left',
        										'locale'=>['format'=>'m-d-y',
														'separator' => 'to']
													]
												]);

                        ActiveForm::end();
                        //                        echo DateRangePicker::widget([
                        //                            'model'=>$model,
                        //                            'attribute'=>'daterange',
                        //                            'convertFormat'=>true,
                        //                            'presetDropdown' => true,
                        //                            'hideInput'=>true,
                        //                            'containerTemplate' => '<span class="input-group-addon">
                        //                                        <i class="glyphicon glyphicon-calendar"></i>
                        //                                    </span>
                        //                                    <span class="form-control text-right">
                        //                                        <span class="pull-left">
                        //                                            <span class="range-value">{value}</span>
                        //                                        </span>
                        //                                        <b class="fa fa-caret-down"></b>
                        //                                        {input}
                        //                                    </span>',
                        //                            'pluginOptions'=>[
                        //                                'timePicker'=>false,
                        //                                'timePickerIncrement'=>30,
                        //                                'locale'=>[
                        //                                    'format'=>'Y-m-d h:i A'
                        //                                ],
                        //                                'opens'=>'left',
                        //                                'separator'=> ' - ',
                        //                            ]
                        //                        ]);
                        ?>
                    </div>
                </div>

            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <?php /*<!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p>Alexander Pierce</p>
                        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                    </div>
                </div>
                <!-- search form -->*/ ?>
                <?php /*<form action="#" method="get" class="sidebar-form">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
                    </div>
                </form>*/ ?>

                <?= Menu::widget() ?>
            </section>
            <section class="logo" style="position: absolute; bottom: 0;">
                <ul class="media-list" style="margin-left: 10px; margin-top: 10px;">
                    <li class="media">
                    <div class="media-left">
                    <a href="#">
                        <?= Html::img('@web/images/user.png', ['class' => 'media-object'] ) ?>
                    </a>
                </div>
                <div class="media-body">
                  <h4 style ="color:white;" class="media-heading"><?= Html::encode(Yii::$app->user->identity->username); ?></h4>
                  <?= Html::a('profile', ['/auth/default/view', 'id' => Yii::$app->user->id], ['class' => 'small'])?>
                  &nbsp; &nbsp;
                  <?= Html::a('logout', ['/site/logout'], ['data-method' => 'post', 'class' => 'small'])?>

              </div>
          </li>
      </ul>

            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
<!--            <section class="content-header">-->
<!--            </section>-->

            <section class="content">
                <?= $content ?>
            </section>
        </div>
        <!-- /.content-wrapper -->

    </div>
    <!-- ./wrapper -->

<?php $this->endContent();
