<?php

namespace app\models\query;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class SpotlightQuery extends \yii\db\ActiveQuery
{
    /**
     * @param null $db
     * @return \app\models\Spotlight[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @param null $db
     * @return \app\models\Spotlight|null|\yii\db\ActiveRecord
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
