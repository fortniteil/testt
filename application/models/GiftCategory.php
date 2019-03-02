<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property int $id
 * @property string $directory
 * @property string $language_category
 * @property string $title
 * @property int $is_visible
 * @property int $created_at
 * @property int $updated_at
 *
 * @property GiftItem[] $giftItems
 */
class GiftCategory extends \yii\db\ActiveRecord
{
    const HIDDEN = 0;
    const VISIBLE = 1;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%gift_category}}';
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
            [['directory', 'title'], 'required'],
            [['language_category'], 'default', 'value' => 'app'],
            [['created_at', 'updated_at'], 'integer'],
            [['is_visible'], 'boolean'],
            [['directory', 'title'], 'string', 'max' => 255],
            [['language_category'], 'string', 'max' => 64],
            [['directory'], 'unique'],
            [['directory'], 'checkDirectory'],
        ];
    }

    public function checkDirectory()
    {
        Yii::$app->giftManager->checkDirectory($this->directory);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'directory' => Yii::t('app', 'Directory'),
            'language_category' => Yii::t('app', 'Language Category'),
            'title' => Yii::t('app', 'Title'),
            'is_visible' => Yii::t('app', 'Visible'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGiftItems()
    {
        return $this->hasMany(GiftItem::class, ['category_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return Yii::t($this->language_category, $this->title);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\GiftCategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\GiftCategoryQuery(get_called_class());
    }
}
