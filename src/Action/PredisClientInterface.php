<?php

namespace Soarce\Action;

use Predis\ClientInterface;

interface PredisClientInterface
{
    /**
     * @param ClientInterface $client
     */
    public function setPredisClient(ClientInterface $client): void;
}