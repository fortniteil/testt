<?php

namespace app\settings;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\settings
 */
class LazySettingsValue
{
    /**
     * @var string
     */
    private $category;
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $default;
    /**
     * @var string
     */
    private $component;

    /**
     * @param $category
     * @param $key
     * @param $default
     * @param string $component
     */
    public function __construct($category, $key, $default = null, $component = 'settings')
    {
        $this->category = $category;
        $this->key = $key;
        $this->default = $default;
        $this->component = $component;
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function getValue()
    {
        /** @var Settings $settings */
        $settings = Yii::$app->get($this->component);
        return $settings->get($this->category, $this->key, $this->default);
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function __invoke()
    {
        return (string) $this->getValue();
    }
}
