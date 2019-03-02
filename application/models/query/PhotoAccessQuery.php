<?php

namespace app\models\query;

use app\models\PhotoAccess;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class PhotoAccessQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return PhotoAccess[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PhotoAccess|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $fromUserId
     * @param $toUserId
     * @return $this
     */
    public function whereUsersAre($fromUserId, $toUserId)
    {
        return $this->andWhere([
            'photo_access.from_user_id' => (int) $fromUserId,
            'photo_access.to_user_id' => (int) $toUserId,
        ]);
    }
}
