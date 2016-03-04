<?php

/* @var $this yii\web\View */
$this->title = 'Trend';

use kartik\select2\Select2;

$this->registerJsFile('@web/js/trend.js', ['depends' => [\frontend\assets\AppAsset::className(), \kartik\select2\Select2Asset::className()]]);
$data = [
    'American' => 'USD',
    'Viet Nam' => 'VND',
    'Russia' => 'Rub',
];
//
//echo Select2::widget([
//    'name' => 'blah1',
//    'data' => $data,
//    'theme' => Select2::THEME_BOOTSTRAP,
//    'options' => ['placeholder' => 'Select a Segment ...'],
//    'pluginOptions' => [
//        'allowClear' => true
//    ],
//]);
?>

<div class="row bg-white save-query">
    <div class="box box-square">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="saved-query" class="col-sm-2 control-label">Saved</label>
                            <div class="col-sm-10">
                                <?= Select2::widget([
                                    'name' => 'save-query',
                                    'theme' => Select2::THEME_BOOTSTRAP,
                                    'options' => [
                                        'placeholder' => 'Select saved query...',
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ]) ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 clearfix">
                    <button class="btn btn-primary btn-flat" data-toggle="modal" data-target="#save-submit"><i class="fa fa-floppy-o"></i> Save</button>
                    <button class="btn btn-default btn-flat disabled">Save as ...</button>
                    <button class="btn btn-default btn-flat disabled"><i class="fa fa-trash-o"></i>&nbsp;</button>
                    <button class="btn btn-default btn-flat pull-right" id="bookmark"><i class="fa fa-star"></i>&nbsp;</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="save-submit" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><strong>Save Query</strong></h4>
            </div>
            <div class="modal-body">
                <p>Enter a name for your query. This name will be seen by others if you share this query.</p>
                <input type="text" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Save</button>
            </div>
        </div>

    </div>
</div>

<div class="row">
    <div class="invoice">
        <div class="row">
            <div class="col-md-6">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="segment" class="col-sm-2 control-label">Segment</label>
                        <div class="col-sm-10">
                            <?= Select2::widget([
                                'name' => 'segment',
                                'theme' => Select2::THEME_BOOTSTRAP,
                                'options' => [
                                    'placeholder' => 'Select segment...',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="action" class="col-sm-2 control-label">Action</label>
                        <div class="col-sm-10">
                            <?= Select2::widget([
                                'name' => 'action',
                                'theme' => Select2::THEME_BOOTSTRAP,
                                'options' => [
                                    'placeholder' => 'Select action...',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="field" class="col-sm-2 control-label">Field</label>
                        <div class="col-sm-10">
                            <?= Select2::widget([
                                'name' => 'field',
                                'theme' => Select2::THEME_BOOTSTRAP,
                                'options' => [
                                    'placeholder' => 'Select field...',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-10 col-md-offset-2">
                            <button class="btn btn-primary btn-flat"><i class="fa fa-play"></i> Query</button>
                            <button class="btn btn-default btn-flat">Advance Editor</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="invoice">
        result
    </div>
</div>


