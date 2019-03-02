<?php

namespace app\models\query;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 * @see \app\models\GiftCategory
 */
class GiftCategoryQuery extends \yii\db\ActiveQuery
{
    /**
     * @return GiftCategoryQuery
     */
    public function visible()
    {
        return $this->andWhere(['gift_category.is_visible' => 1]);
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
