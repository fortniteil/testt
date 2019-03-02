<?php

namespace app\payments;

use app\helpers\Html;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\payments
 */
class GiftTransaction extends TransactionInfo
{
    /**
     * @var int
     */
    public $toUserId;
    /**
     * @var int
     */
    public $giftItemId;
    /**
     * @var string
     */
    public $message;
    /**
     * @var bool
     */
    public $isPrivate;

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE_GIFT;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $user = Yii::$app->userManager->getUserById($this->toUserId);
        if ($user !== null) {
            return Yii::t('app', 'Gift for {0}', Html::a($user->profile->getDisplayName(), ['/profile/view', 'username' => $user->username]));
        } else {
            return Yii::t('app', 'Gift');
        }
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return null;
    }
}
