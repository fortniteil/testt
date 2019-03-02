<?php

namespace app\notifications;

use app\helpers\Url;
use app\helpers\Html;
use app\models\PhotoAccess;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\notifications
 */
class PhotoAccessAction extends BaseNotification
{
    const SETTINGS_EMAIL_KEY = 'receiveEmailOnPhotoAccessAction';

    /**
     * @var string
     */
    public $viewName = 'notifications/photo-access-action';
    /**
     * @var int
     */
    public $sortOrder = 120;

    /**
     * @return BaseNotificationCategory|PhotoAccessActionCategory|null
     */
    public function category()
    {
        return new PhotoAccessActionCategory();
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
        return Yii::t('app', 'Private photos access');
    }

    /**
     * @return null|string
     */
    public function render()
    {
        return $this->html();
    }

    /**
     * @return null|string
     */
    public function html()
    {
        /** @var PhotoAccess $photoAccess */
        $photoAccess = $this->source;
        if ($photoAccess == null) {
            return parent::html();
        }
        if ($photoAccess->status == PhotoAccess::STATUS_APPROVED) {
            return Yii::t('app', 'You now have access to {name} private photos.', [
                'name' => Html::tag('strong',
                    Html::a(Html::encode($this->sender->profile->getDisplayName()),
                        ['/profile/view', 'username' => $this->sender->username])
                ),
            ]);
        } elseif ($photoAccess->status == PhotoAccess::STATUS_REJECTED) {
            return Yii::t('app', '{name} rejected your request to view private photos.', [
                'name' => Html::tag('strong',
                    Html::a(Html::encode($this->sender->profile->getDisplayName()),
                        ['/profile/view', 'username' => $this->sender->username])
                ),
            ]);
        } else {
            return null;
        }
    }
}
