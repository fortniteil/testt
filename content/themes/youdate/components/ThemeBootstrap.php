<?php

namespace youdate\components;

use app\base\Controller;
use yii\base\Event;
use Yii;
use yii\base\BootstrapInterface;
use youdate\assets\Asset;
use youdate\assets\UploadAsset;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\components
 */
class ThemeBootstrap implements BootstrapInterface
{
    /**
     * @var array
     */
    public $rtlLanguages = ['ar', 'az', 'dv', 'he', 'ku', 'fa', 'ur'];

    public function bootstrap($app)
    {
        $app->assetManager->bundles[UploadAsset::class] = [
            'sourcePath' => Yii::getAlias('@theme/static'),
            'css' => [],
            'js' => [
                'js/vendors/filekit.js',
            ]
        ];

        // detect rtl language
        Yii::$container->set(Asset::class, [
            'rtlEnabled' => $this->isRtlLanguage(),
        ]);
        Event::on(Controller::class, Controller::EVENT_AFTER_INIT, function (Event $event) {
            /** @var Controller $controller */
            $controller = $event->sender;
            $controller->view->params['rtlEnabled'] = $this->isRtlLanguage();
        });
    }

    /**
     * @return bool
     */
    protected function isRtlLanguage()
    {
        $code = substr(Yii::$app->language, 0, 2);
        return in_array($code, $this->rtlLanguages);
    }
}
