<?php

namespace app\modules\admin\models;

use app\models\Profile;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\models
 */
class Photo extends \app\models\Photo
{
    const SCENARIO_TOGGLE = 'toggle';

    /**
     * @return array
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            self::SCENARIO_TOGGLE => ['is_verified'],
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'user_id']);
    }

    /**
     * @param bool $save
     * @return bool
     */
    public function approve($save = false)
    {
        $this->is_verified = true;
        if ($save) {
            return $this->save();
        }

        return true;
    }
}
