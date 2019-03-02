<?php

namespace app\modules\admin\controllers;

use app\models\Photo;
use app\models\User;
use app\settings\Settings;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\controllers
 */
class DefaultController extends \app\modules\admin\components\Controller
{
    /**
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['error'] = [
            'class' => 'yii\web\ErrorAction',
        ];

        return $actions;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex()
    {
        /** @var Settings $settings */
        $settings = Yii::$app->settings;

        return $this->render('index', [
            'counters' => $this->stats->getCounters(),
            'info' => [
                'version' => version(),
                'debug' => env('APP_DEBUG'),
                'environment' => env('APP_ENV'),
                'cronHourly' => (int) $settings->get('app', 'cronLastHourlyRun'),
                'cronDaily' => (int) $settings->get('app', 'cronLastDailyRun'),
                'queueSize' => (new \yii\db\Query())->from('{{%queue}}')->count(),
                'memoryLimit' => ini_get('memory_limit'),
                'timeLimit' => ini_get('max_execution_time'),
                'uploadMaxFilesize' => ini_get('upload_max_filesize'),
                'postMaxSize' => ini_get('post_max_size'),
            ],
        ]);
    }
}
