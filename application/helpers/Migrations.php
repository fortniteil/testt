<?php

namespace app\helpers;

use app\components\ConsoleRunner;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\helpers
 */
class Migrations
{
    /**
     * @param $upOrDown
     * @param $migrationPath
     */
    public static function run($upOrDown, $migrationPath)
    {
        if (is_dir($migrationPath)) {
            $consoleRunner = new ConsoleRunner();
            $consoleRunner->run('migrate/' . $upOrDown, [
                'all',
                'migrationPath' => $migrationPath,
                'interactive' => 0,
            ]);
        }
    }
}
