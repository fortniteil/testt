<?php

namespace app\clients;

use app\settings\LazySettingsValue;
use app\traits\EnabledTrait;
use yii\authclient\clients\Google as BaseGoogle;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\clients
 */
class Google extends BaseGoogle implements ClientInterface
{
    use EnabledTrait;

    public function init()
    {
        parent::init();
        if ($this->clientId instanceof LazySettingsValue) {
            $this->clientId = $this->clientId->getValue();
        }
        if ($this->clientSecret instanceof LazySettingsValue) {
            $this->clientSecret = $this->clientSecret->getValue();
        }
    }

    /**
     * @return mixed|null|string
     */
    public function getEmail()
    {
        return isset($this->getUserAttributes()['emails'][0]['value'])
            ? $this->getUserAttributes()['emails'][0]['value']
            : null;
    }

    /**
     * @return mixed|null|string|void
     */
    public function getUsername()
    {
        return;
    }
}
