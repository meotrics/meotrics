<?php
namespace common\widgets;

use yii\web\View;
use yii\bootstrap\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\select2\Select2 as Select2Base;

class Select2 extends Select2Base
{
    public $theme = self::THEME_DEFAULT;

    /**
     * select2 ajax helpers
     */
    public static function remoteClientOptions($url, $clientOptions = [])
    {
        $clientOptions['ajax'] = [
            'url' => Url::to($url),
            'dataType' => 'json',
            'delay' => 250,
            'cache' => true,
            'data' => new JsExpression('function (params) {return params; }'),
            'processResults' => new JsExpression('function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total
                    }
                };
            }'),
        ];

//        $clientOptions['cache'] = true;
        $clientOptions['escapeMarkup'] = new JsExpression('function (markup){return markup}');
        $clientOptions['templateResult'] = new JsExpression('function (item){return item.text}');
        $clientOptions['templateSelection'] = new JsExpression('function (item){return item.text}');

        return $clientOptions;
    }
}