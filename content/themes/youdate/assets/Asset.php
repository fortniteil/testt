<?php

namespace youdate\assets;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\assets
 */
class Asset extends \yii\web\AssetBundle
{
    public $basePath = '@theme/static';
    public $baseUrl = '@themeUrl/static';
    public $rtlEnabled = false;
    public $js = [
        'js/app.js',
    ];
    public $depends = [
        CoreAsset::class,
    ];

    public function init()
    {
        parent::init();
        if ($this->rtlEnabled == true) {
            $this->css[] = 'css/app.rtl.min.css';
        } else {
            $this->css[] = 'css/app.min.css';
        }
    }
}
