<?php

namespace app\forms;

use app\models\Gift;
use app\models\GiftItem;
use app\models\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class GiftForm extends \yii\base\Model
{
    /**
     * @var string
     */
    public $message;
    /**
     * @var integer
     */
    public $giftItemId;
    /**
     * @var integer
     */
    public $fromUserId;
    /**
     * @var integer
     */
    public $toUserId;
    /**
     * @var bool
     */
    public $isPrivate;

    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            [['giftItemId'], 'required'],
            [['giftItemId', 'toUserId'], 'integer'],
            [['message'], 'string', 'max' => 64],
            [['toUserId'], 'exist',
                'targetClass' => User::class,
                'targetAttribute' => ['toUserId' => 'id']
            ],
            [['giftItemId'], 'exist',
                'targetClass' => GiftItem::class,
                'targetAttribute' => ['giftItemId' => 'id'],
                'filter' => function(ActiveQuery $query) {
                    $query->joinWith(['category']);
                    $query->andWhere([
                        'gift_item.is_visible' => 1,
                        'gift_category.is_visible' => 1,
                    ]);
                }
            ],
            [['fromUserId'], 'compare', 'compareAttribute' => 'toUserId', 'operator' => '!='],
            [['isPrivate'], 'boolean'],
            [['giftItemId'], 'checkAlreadySent'],
        ];

        return $rules;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function validateUserBalance()
    {
        $gift = Yii::$app->giftManager->getItemById($this->giftItemId);

        if (!Yii::$app->balanceManager->hasEnoughCredits($this->fromUserId, $gift->getPrice())) {
            $this->addError('giftItemId', Yii::t('app', 'You don\'t have enough credits for this operation'));
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function checkAlreadySent()
    {
        $alreadySent = Gift::find()->where([
            'from_user_id' => $this->fromUserId,
            'to_user_id' => $this->toUserId,
            'gift_item_id' => $this->giftItemId,
        ])->count() > 0;

        if ($alreadySent) {
            $this->addError('giftItemId', Yii::t('app', 'You have already sent this gift to this user'));
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'fromUserId' => Yii::t('app', 'From User'),
            'toUserId' => Yii::t('app', 'To User'),
            'giftItemId' => Yii::t('app', 'Gift'),
            'message' => Yii::t('app', 'Message'),
            'isPrivate' => Yii::t('app', 'Private gift'),
        ];
    }
}
