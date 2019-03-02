<?php

namespace app\components;

use Yii;
use yii\base\Component;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\components
 */
class ConsoleRunner extends Component
{
    /**
     * @param $route
     * @param $params
     * @return bool
     */
    public function run($route, $params = [])
    {
        defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
        defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

        $oldApp = Yii::$app;
        $consoleConfig = require(Yii::getAlias('@app/config/console.php'));

        try {
            ob_start();
            new \yii\console\Application($consoleConfig);
            Yii::$app->runAction($route, $params);
            @file_put_contents(Yii::getAlias('@app/runtime/logs/migrations.log'), ob_get_clean());
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
        }

        Yii::$app = $oldApp;

        return true;
    }
}
