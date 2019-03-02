<?php

namespace app\clients;

use app\settings\LazySettingsValue;
use app\traits\EnabledTrait;
use Yii;
use yii\authclient\clients\Yandex as BaseYandex;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\clients
 */
class Yandex extends BaseYandex implements ClientInterface
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
        $emails = isset($this->getUserAttributes()['emails'])
            ? $this->getUserAttributes()['emails']
            : null;

        if ($emails !== null && isset($emails[0])) {
            return $emails[0];
        } else {
            return null;
        }
    }

    /**
     * @return mixed|null|string
     */
    public function getUsername()
    {
        return isset($this->getUserAttributes()['login'])
            ? $this->getUserAttributes()['login']
            : null;
    }

    /**
     * @return string
     */
    protected function defaultTitle()
    {
        return Yii::t('app', 'Yandex');
    }
}
