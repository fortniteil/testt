<?php

namespace app\modules\admin\widgets;

use app\modules\admin\assets\SelectizeAsset;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\widgets
 */
class SelectizeInputWidget extends \yii\widgets\InputWidget
{
    /**
     * @var string
     */
    public $loadUrl;
    /**
     * @var string the parameter name
     */
    public $queryParam = 'query';
    /**
     * @var array
     */
    public $clientOptions;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $id = $this->options['id'];
        if ($this->loadUrl !== null) {
            $url = Url::to($this->loadUrl);
            $this->clientOptions['load'] = new JsExpression("function (query, callback) { if (!query.length) return callback(); $.getJSON('$url', { {$this->queryParam}: query }, function (data) { callback(data); }).fail(function () { callback(); }); }");
        }
        $options = Json::encode($this->clientOptions);
        $view = $this->getView();
        SelectizeAsset::register($view);
        $view->registerJs("jQuery('#$id').selectize($options);");
    }
}
