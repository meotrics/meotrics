<?php

namespace frontend\widgets;


use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Box extends Widget
{
    private $tag = 'div';
    public $options = [];
    public $styles = [];

    public $title;
    public $headerOptions = [];
    public $bodyOptions = [];

    public function init()
    {
        if (isset($this->options)) {
            $this->tag = ArrayHelper::getValue($this->options, 'tag', 'div');

            if (empty($this->options['id'])) {
                $this->options['id'] = $this->id;
            }

            Html::addCssClass($this->options, 'box');
            foreach ($this->styles as $style) {
                Html::addCssClass($this->options, 'box-' . $style);
            }
        }

        Html::addCssClass($this->headerOptions, 'box-header');
        Html::addCssClass($this->bodyOptions, 'box-body');
        echo Html::beginTag($this->tag, $this->options);
        echo $this->renderHeader();
        echo Html::beginTag('div', $this->bodyOptions);
    }

    private function renderHeader() {
        if (empty($this->title)) {
            return '';
        }

        $title = Html::tag('h3', $this->title, ['class' => 'box-title']);
        return Html::tag('div', $title, $this->headerOptions);
    }

    public function run()
    {
        echo Html::endTag('div');
        echo Html::endTag($this->tag);
    }
}