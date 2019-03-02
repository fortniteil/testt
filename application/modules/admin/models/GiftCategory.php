<?php

namespace app\modules\admin\models;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\models
 */
class GiftCategory extends \app\models\GiftCategory
{
    const SCENARIO_TOGGLE = 'toggle';

    /**
     * @return array
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            self::SCENARIO_TOGGLE => ['is_visible'],
        ]);
    }
}
