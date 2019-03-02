<?php

namespace app\modules\admin\assets;

use yii\web\AssetBundle;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\assets
 */
class SelectizeAsset extends AssetBundle
{
    public $sourcePath = '@bower/selectize/dist';
    public $css = [
        'css/selectize.bootstrap3.css',
    ];
    public $js = [
        'js/standalone/selectize.js',
    ];
    public $depends = [
        \yii\bootstrap\BootstrapAsset::class,
        \yii\web\JqueryAsset::class,
    ];
}
