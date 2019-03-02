<?php

namespace app\notifications;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\notifications
 */
class PhotoAccessActionCategory extends BaseNotificationCategory
{
    /**
     * @var string
     */
    public $id = 'photoAccess';

    /**
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('app', 'Photo access requests');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return Yii::t('app', 'Receive Notifications for private photos access status');
    }
}
