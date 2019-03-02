<?php

namespace app\notifications;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\notifications
 */
class GiftReceivedCategory extends BaseNotificationCategory
{
    /**
     * @var string
     */
    public $id = 'gifts';

    /**
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('app', 'Gifts');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return Yii::t('app', 'Receive Notifications for incoming gifts');
    }
}
