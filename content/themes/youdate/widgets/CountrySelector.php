<?php

namespace youdate\widgets;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class CountrySelector extends SelectizeDropDownList
{
    public $options = ['class' => 'country-selector'];

    public function init()
    {
        parent::init();
        $this->items = array_merge([null => Yii::t('youdate', 'Country')], Yii::$app->geographer->getCountriesList());
    }
}
