<?php

namespace Soarce\Action;

use Soarce\Action;
use Soarce\Config;

class Start extends Action
{
    /**
     * @return string
     * @throws Exception
     */
    public function run(): string
    {
        if (!is_writable($this->config->getDataPath()) || false === touch($this->config->getDataPath() . DIRECTORY_SEPARATOR . Config::TRIGGER_FILENAME)) {
            throw new Exception('data dir does not exist, is not writable or full', Exception::DATA_DIRECTORY__NOT_WRITEABLE);
        }

        return json_encode(['status' => 'OK']);
    }
}
