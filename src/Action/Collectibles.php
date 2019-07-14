<?php

namespace Soarce\Action;

use Soarce\Action;

class Collectibles extends Action
{
    /**
     * @return string
     */
    public function run(): string
    {
        if (!is_readable($this->config->getDataPath()) || !is_dir($this->config->getDataPath())) {
            throw new Exception('data dir does not exist, is not writable or full', Exception::DATA_DIRECTORY__NOT_READABLE);
        }

        $data = [];

        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
