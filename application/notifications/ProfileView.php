<?php

namespace app\notifications;

use app\helpers\Url;
use Yii;
use app\helpers\Html;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\notifications
 */
class ProfileView extends BaseNotification
{
    const SETTINGS_EMAIL_KEY = 'receiveEmailOnPhotoView';

    /**
     * @var string
     */
    public $viewName = 'notifications/profile-view';
    /**
     * @var int
     */
    public $sortOrder = 110;

    /**
     * @return BaseNotificationCategory|ProfileViewCategory|null
     */
    public function category()
    {
        return new ProfileViewCategory();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return Url::to(['/connections/guests']);
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
        return Yii::t('app', 'New profile view');
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
        return Yii::t('app', '{name} viewed your profile.', [
            'name' => Html::tag('strong',
                Html::a(Html::encode($this->sender->profile->getDisplayName()),
                    ['/profile/view', 'username' => $this->sender->username])
            ),
        ]);
    }
}
