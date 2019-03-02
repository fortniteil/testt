<?php

namespace app\widgets;

use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\widgets
 */
class Menu extends \yii\widgets\Menu
{
    public function sortItems()
    {
        $order = 0;
        foreach ($this->items as $k => $item) {
            if (!isset($item['order'])) {
                $this->items[$k]['order'] = $order;
            } else {
                $order = $item['order'];
            }
        }

        $orders = ArrayHelper::getColumn($this->items, 'order');
        array_multisort($orders, SORT_ASC, $this->items);
    }

    public function beforeRun()
    {
        $this->sortItems();
        return parent::beforeRun();
    }
}
