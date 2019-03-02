<?php

namespace youdate\widgets;

use app\models\Profile;
use app\models\User;
use Yii;
use yii\base\Widget;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class GiftsWidget extends Widget
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
     * @return string
     */
    public function run()
    {
        $gifts = Yii::$app->giftManager->getUserGifts($this->user);

        return $this->render('gifts/widget', [
            'gifts' => $gifts,
            'currentUserId' => Yii::$app->user->id,
            'isCurrentUser' => Yii::$app->user->id == $this->user->id,
            'user' => $this->user,
            'profile' => $this->profile,
        ]);
    }
}
