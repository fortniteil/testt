<?php

namespace app\base;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\base
 */
class View extends \yii\web\View
{
    const EVENT_CUSTOM_HEADER = 'customHeader';
    const EVENT_CUSTOM_FOOTER = 'customFooter';

    /**
     * @var string
     */
    protected $themePath;
    /**
     * @var string
     */
    protected $extendedThemePath;

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function frontendSetting($key, $default = null)
    {
        if (!isset(Yii::$app->params['frontend'])) {
            return $default;
        }

        return ArrayHelper::getValue(Yii::$app->params['frontend'], $key, $default);
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function themeSetting($key, $default = null)
    {
        if (!isset(Yii::$app->params['theme'])) {
            return $default;
        }

        return ArrayHelper::getValue(Yii::$app->params['theme'], $key, $default);
    }

    /**
     * @param string $viewFile
     * @param array $params
     * @param null $context
     * @return string
     */
    public function renderFile($viewFile, $params = [], $context = null)
    {
        if (!Yii::$app->themeManager->isExtendedTheme) {
            return parent::renderFile($viewFile, $params, $context);
        }

        $files = [];
        $files[] = str_replace($this->getThemePath(), $this->getExtendedThemePath(), $viewFile);
        $files[] = Yii::getAlias(str_replace('@theme', '@extendedTheme', $viewFile));

        foreach ($files as $file) {
            if (is_file($file)) {
                return parent::renderFile($file, $params, $context);
            }
        }

        return parent::renderFile($viewFile, $params, $context);
    }

    public function customHeaderCode()
    {
        $this->trigger(self::EVENT_CUSTOM_HEADER);
    }

    public function customFooterCode()
    {
        $this->trigger(self::EVENT_CUSTOM_FOOTER);
    }

    /**
     * @return string
     */
    public function getThemePath()
    {
        if (!isset($this->themePath)) {
            $this->themePath = Yii::getAlias('@theme');
        }

        return $this->themePath;
    }

    /**
     * @return string
     */
    public function getExtendedThemePath()
    {
        if (!isset($this->extendedThemePath)) {
            $this->extendedThemePath = Yii::getAlias('@extendedTheme');
        }

        return $this->extendedThemePath;
    }
}
