<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property int $id
 * @property int $gift_item_id
 * @property int $from_user_id
 * @property int $to_user_id
 * @property int $is_private
 * @property string $message
 * @property int $created_at
 *
 * @property User $fromUser
 * @property GiftItem $giftItem
 * @property User $toUser
 */
class Gift extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     * @return \app\models\query\GiftQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\GiftQuery(get_called_class());
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%gift}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gift_item_id', 'to_user_id'], 'required'],
            [['gift_item_id', 'from_user_id', 'to_user_id', 'is_private', 'created_at'], 'integer'],
            [['message'], 'string', 'max' => 64],
            [['from_user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['from_user_id' => 'id']
            ],
            [['gift_item_id'], 'exist', 'skipOnError' => true,
                'targetClass' => GiftItem::class,
                'targetAttribute' => ['gift_item_id' => 'id']
            ],
            [['to_user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['to_user_id' => 'id']
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'gift_item_id' => Yii::t('app', 'Gift'),
            'from_user_id' => Yii::t('app', 'From User'),
            'to_user_id' => Yii::t('app', 'To User'),
            'is_private' => Yii::t('app', 'Is Private'),
            'message' => Yii::t('app', 'Message'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser()
    {
        return $this->hasOne(User::class, ['id' => 'from_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGiftItem()
    {
        return $this->hasOne(GiftItem::class, ['id' => 'gift_item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne(User::class, ['id' => 'to_user_id']);
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        return preg_replace('/\s+/S', " ", $this->message);
    }
}
