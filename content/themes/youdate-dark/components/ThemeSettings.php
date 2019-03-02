<?php

namespace youdateDark\components;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdateDark\components
 */
class ThemeSettings extends \youdate\components\ThemeSettings
{
    /**
     * @return array
     */
    public function getSettings()
    {
        $settings = [
            // your custom settings
            // see `content/themes/youdate/components/ThemeSettings.php
            // ...
        ];

        return array_merge($settings, parent::getSettings());
    }
}
