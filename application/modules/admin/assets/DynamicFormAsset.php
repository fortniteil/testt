<?php

namespace app\modules\admin\assets;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\assets
 */
class DynamicFormAsset extends \yii\web\AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/modules/admin/static';
    public $js = [
        YII_DEBUG ? 'js/dynamic-form.js' : 'js/dynamic-form.min.js',
    ];
    /**
     * @var array
     */
    public $depends = [
        \yii\web\JqueryAsset::class,
        \yii\widgets\ActiveFormAsset::class,
    ];
}
