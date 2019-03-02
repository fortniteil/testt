<?php

namespace app\themes;

use app\settings\HasSettings;
use yii\base\BaseObject;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\themes
 */
class ThemeSettings extends BaseObject implements HasSettings
{
    /**
     * @return array
     */
    public function getSettings()
    {
        return [];
    }
}
