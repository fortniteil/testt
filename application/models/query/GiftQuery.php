<?php

namespace app\models\query;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 * @see \app\models\Gift
 */
class GiftQuery extends \yii\db\ActiveQuery
{
    /**
     * @return GiftQuery
     */
    public function latest()
    {
        return $this->orderBy('gift.id desc');
    }

    /**
     * @param $userId
     * @return GiftQuery
     */
    public function forUser($userId)
    {
        return $this->andWhere(['gift.to_user_id' => $userId]);
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
