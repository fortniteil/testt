<?php

namespace app\clients;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\clients
 */
interface ClientInterface extends \yii\authclient\ClientInterface
{
    /**
     * @return mixed|string|null
     */
    public function getEmail();

    /**
     * @return mixed|string|null
     */
    public function getUsername();

    /**
     * @return bool
     */
    public function isEnabled();
}
