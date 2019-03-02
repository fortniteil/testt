<?php

namespace app\traits;

use app\settings\LazySettingsValue;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\traits
 */
trait EnabledTrait
{
    /**
     * @var bool
     */
    public $enabled;

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function isEnabled()
    {
        if (is_bool($this->enabled)) {
            return $this->enabled;
        }

        if ($this->enabled instanceof LazySettingsValue) {
            return (bool) $this->enabled->getValue();
        }

        return $this->enabled;
    }
}
