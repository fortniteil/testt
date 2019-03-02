<?php

namespace app\components;

use yii\base\Exception;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\components
 */
class AppException extends Exception
{
    const LEVEL_WARNING = 'warning';
    const LEVEL_DANGER = 'danger';
    const LEVEL_INFO = 'info';

    public $level;

    /**
     * AppException constructor.
     * @param null $message
     * @param string $level
     * @param \Exception|null $previous
     */
    public function __construct($message = null, $level = self::LEVEL_DANGER, \Exception $previous = null)
    {
        $this->level = $level;
        parent::__construct($message, $this->code, $previous);
    }
}
