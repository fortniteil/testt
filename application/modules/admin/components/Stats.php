<?php

namespace app\modules\admin\components;

use app\models\Photo;
use app\models\User;
use app\modules\admin\models\Report;
use app\modules\admin\models\Verification;
use yii\base\Component;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\components
 */
class Stats extends Component
{
    /**
     * @var array
     */
    protected $counters;

    /**
     * @return array
     */
    public function getCounters()
    {
        if (isset($this->counters)) {
            return $this->counters;
        }

        $this->counters = [
            'users' => User::find()->count(),
            'usersOnline' => User::find()->online()->count(),
            'photos' => Photo::find()->count(),
            'photosUnverified' => Photo::find()->unverified()->count(),
            'reportsNew' => Report::find()->newOnly()->count(),
            'verificationsNew' => Verification::find()->newOnly()->count(),
        ];

        return $this->counters;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getCount($key, $default = null)
    {
        return $this->counters[$key] ?? $default;
    }
}
