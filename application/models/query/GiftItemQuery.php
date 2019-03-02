<?php

namespace app\models\query;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 * @see \app\models\GiftItem
 */
class GiftItemQuery extends \yii\db\ActiveQuery
{
    /**
     * @return GiftItemQuery
     */
    public function visible()
    {
        return $this->andWhere(['gift_item.is_visible' => 1]);
    }

    /**
     * @param null $db
     * @return array|\yii\db\ActiveRecord[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @param null $db
     * @return array|null|\yii\db\ActiveRecord
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
