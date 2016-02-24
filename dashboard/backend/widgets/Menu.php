<?php
/**
 * Created by PhpStorm.
 * User: vuquangthinh
 * Date: 1/3/2016
 * Time: 10:25 PM
 */

namespace backend\widgets;

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
            [
                'label' => 'Trang quản trị', 'url' => ['/site/index'],
                'icon' => 'fa fa-dashboard',
            ],
            [
                'label' => 'Trang web',
                'url' => 'http://dulibu.dev/frontend/web/',
                'icon' => 'fa fa-globe',
            ],
            self::createHeader('NGƯỜI DÙNG'),
            [
                'label' => 'Người dùng',
                'url' => ['/user/default/index'],
                'icon' => 'fa fa-users',
            ],
            [
                'label' => 'Phân quyền',
                'url' => ['/user/rbac/default/index'],
                'icon' => 'fa fa-bolt',
            ],
            [
                'label' => 'Quản lý',
                'url' => ['/auth/default/index'],
                'icon' => 'fa fa-user-md',
            ],
            self::createHeader('ĐỊA ĐIỂM'),
            [
                'label' => 'Địa điểm',
                'url' => ['/place/default/index'],
                'icon' => 'fa fa-globe',
            ],
            self::createHeader('BÀI VIẾT'),
            [
                'label' => 'Bài viết',
                'url' => ['/blog/default/index'],
                'icon' => 'fa fa-file',
            ],
            self::createHeader('HỆ THỐNG'),
            [
                'label' => 'Cấu hình',
                'url' => ['/site/config'],
                'icon' => 'fa fa-cog',
            ],
            [
                'label' => 'Cache',
                'url' => ['/site/cache'],
                'icon' => 'fa fa-exchange',
            ],
            [
                'label' =>'Hiệu năng',
                'url' => ['/site/monitor'],
                'icon' => 'fa fa-cogs',
            ],
            [
                'label' => 'Thông tin',
                'url' => ['/site/info'],
                'icon' => 'fa fa-info',
            ],
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