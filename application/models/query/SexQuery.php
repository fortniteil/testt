<?php

namespace app\models\query;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class SexQuery extends \yii\db\ActiveQuery
{
    /**
     * @param null $db
     * @return \app\models\Sex[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @param null $db
     * @return \app\models\Sex|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
