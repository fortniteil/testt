<?php

namespace app\clients;

use yii\authclient\Collection;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\clients
 */
class ClientCollection extends Collection
{
    /**
     * @return array|\yii\authclient\ClientInterface[]
     */
    public function getClients()
    {
        $clients = parent::getClients();
        $filteredClients = [];
        foreach ($clients as $id => $client) {
            if ($client instanceof ClientInterface && $client->isEnabled()) {
                $filteredClients[$id] = $client;
            }
        }

        return $filteredClients;
    }
}
