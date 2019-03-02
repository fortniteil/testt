<?php

namespace youdateDark\components;

use yii\base\Event;
use yii\base\BootstrapInterface;
use app\base\View;
use youdate\assets\Asset;
use youdateDark\assets\DarkAsset;

class ThemeBootstrap extends \youdate\components\ThemeBootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);
        $this->initEvents();
    }

    public function initEvents()
    {
        Event::on(View::class, View::EVENT_CUSTOM_HEADER, function (Event $event) {
            /** @var View $view */
            $view = $event->sender;
            $view->registerAssetBundle(DarkAsset::class, ['extends' => Asset::class]);
        });
    }
}
