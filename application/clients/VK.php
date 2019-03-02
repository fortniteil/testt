<?php

namespace app\clients;

use app\settings\LazySettingsValue;
use app\traits\EnabledTrait;
use Yii;
use yii\authclient\clients\VKontakte as BaseVKontakte;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\clients
 */
class VK extends BaseVKontakte implements ClientInterface
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
     * @var string
     */
    public $scope = 'email';

    /**
     * @return mixed|null|string
     */
    public function getEmail()
    {
        return $this->getAccessToken()->getParam('email');
    }

    /**
     * @return mixed|null|string
     */
    public function getUsername()
    {
        return isset($this->getUserAttributes()['screen_name'])
            ? $this->getUserAttributes()['screen_name']
            : null;
    }

    /**
     * @return string
     */
    protected function defaultTitle()
    {
        return Yii::t('app', 'VK');
    }

    /**
     * @return string
     */
    protected function defaultName()
    {
        return 'vk';
    }
}
