<?php

namespace youdateDark\assets;

use yii\web\AssetBundle;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package mydate\assets
 */
class DarkAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $basePath = '@extendedTheme/static';
    /**
     * @var string
     */
    public $baseUrl = '@extendedThemeUrl/static';
    /**
     * @var array
     */
    public $css = ['css/youdate-dark.css'];
}
