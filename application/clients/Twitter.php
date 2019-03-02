<?php

namespace app\clients;

use app\settings\LazySettingsValue;
use app\traits\EnabledTrait;
use yii\authclient\clients\Twitter as BaseTwitter;
use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\clients
 */
class Twitter extends BaseTwitter implements ClientInterface
{
    use EnabledTrait;

    public function init()
    {
        parent::init();
        if ($this->consumerKey instanceof LazySettingsValue) {
            $this->consumerKey = $this->consumerKey->getValue();
        }
        if ($this->consumerSecret instanceof LazySettingsValue) {
            $this->consumerSecret = $this->consumerSecret->getValue();
        }
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return ArrayHelper::getValue($this->getUserAttributes(), 'screen_name');
    }

    /**
     * @return string|null User's email, Twitter does not provide user's email address
     * unless elevated permissions have been granted
     * https://dev.twitter.com/rest/reference/get/account/verify_credentials
     */
    public function getEmail()
    {
        return ArrayHelper::getValue($this->getUserAttributes(), 'email');
    }

    /**
     * @return mixed|string
     */
    public function getReturnUrl()
    {
        $redirectUrl = env('SOCIAL_TWITTER_REDIRECT_URL', false);
        if (!empty($redirectUrl) && $redirectUrl !== false) {
            return $redirectUrl;
        }

        return parent::getReturnUrl();
    }
}
