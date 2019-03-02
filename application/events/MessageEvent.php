<?php

namespace app\events;

use app\models\Message;
use app\base\Event;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\events
 */
class MessageEvent extends Event
{
    /**
     * @var Message
     */
    public $message;
}
