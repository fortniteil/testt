<?php

namespace app\notifications;

use app\helpers\Url;
use app\helpers\Html;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\notifications
 */
class PhotoAccessRequest extends BaseNotification
{
    const SETTINGS_EMAIL_KEY = 'receiveEmailOnPhotoAccessRequest';

    /**
     * @var string
     */
    public $viewName = 'notifications/photo-access-request';
    /**
     * @var int
     */
    public $sortOrder = 120;

    /**
     * @return BaseNotificationCategory|PhotoAccessRequestCategory|null
     */
    public function category()
    {
        return new PhotoAccessRequestCategory();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return Url::to(['/settings/access-requests']);
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
        return Yii::t('app', 'Private photos access request');
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
        return Yii::t('app', '{name} requested access to {privatePhotosLink}.', [
            'name' => Html::tag('strong',
                Html::a(Html::encode($this->sender->profile->getDisplayName()),
                    ['/profile/view', 'username' => $this->sender->username])
            ),
            'privatePhotosLink' => Html::a(Yii::t('app', 'your private photos'), ['/settings/access-requests']),
        ]);
    }
}
