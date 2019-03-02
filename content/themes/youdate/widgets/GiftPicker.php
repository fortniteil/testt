<?php

namespace youdate\widgets;

use app\forms\GiftForm;
use app\models\Profile;
use app\models\User;
use Yii;
use yii\base\Widget;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class GiftPicker extends Widget
{
    /**
     * @var User
     */
    public $user;
    /**
     * @var Profile
     */
    public $profile;
    /**
     * @var string|null
     */
    public $pjaxContainer = null;

    /**
     * @return string
     */
    public function run()
    {
        return $this->render('gifts/picker', [
            'user' => $this->user,
            'profile' => $this->profile,
            'pjaxContainer' => $this->pjaxContainer,
            'giftForm' => new GiftForm(),
            'giftItems' => Yii::$app->giftManager->getGiftItems(),
        ]);
    }
}
