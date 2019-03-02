<?php

namespace app\notifications;

use app\helpers\Url;
use app\managers\LikeManager;
use app\helpers\Html;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\notifications
 */
class ProfileLike extends BaseNotification
{
    const SETTINGS_EMAIL_KEY = 'receiveEmailOnProfileLike';

    /**
     * @var string
     */
    public $viewName = 'notifications/profile-like';
    /**
     * @var int
     */
    public $sortOrder = 100;

    /**
     * @return BaseNotificationCategory|ProfileLikeCategory|null
     */
    public function category()
    {
        return new ProfileLikeCategory();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return Url::to(['/connections/likes', 'type' => LikeManager::TYPE_TO_CURRENT_USER]);
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
        return Yii::t('app', 'Someone likes you!');
    }

    /**
     * @return string
     */
    public function html()
    {
        return Yii::t('app', '{name} liked you.', [
            'name' => Html::tag('strong',
                Html::a(Html::encode($this->sender->profile->getDisplayName()),
                    ['/profile/view', 'username' => $this->sender->username])
            ),
        ]);
    }
}
