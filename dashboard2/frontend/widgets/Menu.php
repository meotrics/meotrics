<?php

namespace frontend\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Menu extends \yii\widgets\Menu
{
    public function init()
    {
        parent::init();

        $this->options['class'] = 'sidebar-menu';
        $this->submenuTemplate = "\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n";
        $this->activateParents = true;

        // init items
        $this->items = [
            // Important: you need to specify url as 'controller/action',
            // not just as 'controller' even if default action is used.

            self::createHeader('CÔNG CỤ'),
            [
                'label' => 'Trend',
                'url' => ['#'],
                'icon' => 'fa fa-exchange',
            ],
            [
                'label' => 'Segmentation',
                'url' => ['#'],
                'icon' => 'fa fa-bolt',
            ],

            self::createHeader('HƯỚNG DẪN'),
            [
                'label' => 'Cấu hình',
                'url' => 'http://docs.meotrics.com/',
                'icon' => 'fa fa-book',
            ],
//            self::createHeader('Users'),
//            [
//                'label' => 'Trend',
//                'url' => ['/site/'],
//                'icon' => 'fa fa-exchange',
//            ],
//            [
//                'label' => 'Segmentation',
//                'url' => ['/site/'],
//                'icon' => 'fa fa-bolt',
//            ],
        ];
    }

    public static function createHeader($content)
    {
        return [
            'options' => ['class' => 'header'],
            'label' => $content,
        ];
    }

    protected function renderItems($items)
    {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];

            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            if (!empty($class)) {
                if (empty($options['class'])) {
                    $options['class'] = implode(' ', $class);
                } else {
                    $options['class'] .= ' ' . implode(' ', $class);
                }
            }

            if (isset($item['icon'])) {
                $item['label'] = Html::tag('i', '', ['class' => $item['icon']]) . ' ' . Html::tag('span', $item['label']);
            }

            if (!empty($item['items'])) {
                $item['label'] .= Html::tag('i', '', ['class' => 'fa fa-angle-left pull-right']);
            }

            $menu = $this->renderItem($item);
            if (!empty($item['items'])) {
                Html::addCssClass($options, 'treeview');

                $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
                $menu .= strtr($submenuTemplate, [
                    '{items}' => $this->renderItems($item['items']),
                ]);
            }

            if ($tag === false) {
                $lines[] = $menu;
            } else {
                $lines[] = Html::tag($tag, $menu, $options);
            }
        }

        return implode("\n", $lines);
    }

    private static function t($m, $p = [], $l = null)
    {
        return Yii::t('backend', $m, $p, $l);
    }
}