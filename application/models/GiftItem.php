<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property int $id
 * @property int $category_id
 * @property string $file
 * @property string $language_category
 * @property string $title
 * @property int $price
 * @property boolean $is_visible
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Gift[] $gifts
 * @property GiftCategory $category
 */
class GiftItem extends \yii\db\ActiveRecord
{
    const HIDDEN = 0;
    const VISIBLE = 1;

    /**
     * {@inheritdoc}
     * @return \app\models\query\GiftItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\GiftItemQuery(get_called_class());
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%gift_item}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['category_id', 'file'], 'required'],
            [['category_id', 'created_at', 'updated_at'], 'integer'],
            [['file'], 'string', 'max' => 255],
            [['is_visible'], 'boolean'],
            [['category_id'], 'exist', 'skipOnError' => true,
                'targetClass' => GiftCategory::class,
                'targetAttribute' => ['category_id' => 'id']
            ],
            [['title'], 'string', 'max' => 255],
            [['language_category'], 'string', 'max' => 64],
            [['language_category'], 'default', 'value' => 'app'],
            [['category_id', 'file'], 'unique', 'targetAttribute' => ['category_id', 'file']],
            [['price'], 'number', 'integerOnly' => true, 'min' => 0, 'max' => 10000],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category_id' => Yii::t('app', 'Category'),
            'file' => Yii::t('app', 'File'),
            'language_category' => Yii::t('app', 'Language Category'),
            'title' => Yii::t('app', 'Title'),
            'price' => Yii::t('app', 'Price'),
            'is_visible' => Yii::t('app', 'Visible'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGifts()
    {
        return $this->hasMany(Gift::class, ['gift_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(GiftCategory::class, ['id' => 'category_id']);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return Yii::t($this->language_category, $this->title);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return Yii::$app->giftManager->getItemUrl($this);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getPrice()
    {
        return $this->price ?? Yii::$app->settings->get('common', 'priceGift');
    }
}
