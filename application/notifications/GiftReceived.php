<?php

namespace app\notifications;

use app\helpers\Url;
use Yii;
use app\helpers\Html;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\notifications
 */
class GiftReceived extends BaseNotification
{
    const SETTINGS_EMAIL_KEY = 'receiveEmailOnGifts';

    /**
     * @var string
     */
    public $viewName = 'notifications/gift-received';
    /**
     * @var int
     */
    public $sortOrder = 120;

    /**
     * @return BaseNotificationCategory|GiftReceivedCategory|null
     */
    public function category()
    {
        return new GiftReceivedCategory();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return Url::to(['/profile/view']);
    }

    /**
     * @return string
     */
    public function getUserSettingsKey()
    {
        return self::SETTINGS_EMAIL_KEY;
    }

    /**
     * @return string
     */
    public function getMailSubject()
    {
        return Yii::t('app', 'New gift received');
    }

    /**
     * @return null|string
     */
    public function render()
    {
        return $this->html();
    }

    /**
     * @return string
     */
    public function html()
    {
        return Yii::t('app', '{name} sent you a gift.', [
            'name' => Html::tag('strong',
                Html::a(Html::encode($this->sender->profile->getDisplayName()),
                    ['/profile/view', 'username' => $this->sender->username])
            ),
        ]);
    }
}
