<?php

namespace app\modules\admin\components;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\components
 */
class Controller extends \app\base\Controller
{
    /**
     * @var string
     */
    public $layout = 'main';
    /**
     * @var Stats
     */
    protected $stats;

    public function init()
    {
        parent::init();
        $this->trigger(self::EVENT_BEFORE_INIT);
        $this->initData();
        $this->trigger(self::EVENT_AFTER_INIT);
    }

    protected function initData()
    {
        $this->stats = new Stats();
        foreach ($this->stats->getCounters() as $key => $value) {
            $this->view->params["admin.counters.$key"] = $value;
        }
    }
}
