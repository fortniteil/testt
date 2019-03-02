<?php

namespace app\modules\admin\widgets;

use app\helpers\Html;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\widgets;
 */
class CountrySelector extends SelectizeDropDownList
{
    public function init()
    {
        parent::init();
        Html::addCssClass($this->options, 'country-selector');
    }
}
