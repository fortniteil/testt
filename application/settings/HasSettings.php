<?php

namespace app\settings;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\settings
 */
interface HasSettings
{
    /**
     * @return array
     */
    public function getSettings();
}
