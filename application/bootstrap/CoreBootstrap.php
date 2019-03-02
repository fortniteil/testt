<?php

namespace app\bootstrap;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\bootstrap
 */
class CoreBootstrap extends \yii\base\BaseObject implements \yii\base\BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        Yii::$container->set(\yii\i18n\PhpMessageSource::class, [
            'class' => \app\components\PhpMessageSource::class,
        ]);
    }
}
