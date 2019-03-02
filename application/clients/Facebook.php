<?php

namespace app\clients;

use app\settings\LazySettingsValue;
use app\traits\EnabledTrait;
use yii\authclient\clients\Facebook as BaseFacebook;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\clients
 */
class Facebook extends BaseFacebook implements ClientInterface
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
        return isset($this->getUserAttributes()['email'])
            ? $this->getUserAttributes()['email']
            : null;
    }

    /**
     * @return mixed|null|string|void
     */
    public function getUsername()
    {
        return;
    }

    /**
     * @return mixed|string
     */
    public function getReturnUrl()
    {
        $redirectUrl = env('SOCIAL_FACEBOOK_REDIRECT_URL', false);
        if (!empty($redirectUrl) && $redirectUrl !== false) {
            return $redirectUrl;
        }

        return parent::getReturnUrl();
    }
}
