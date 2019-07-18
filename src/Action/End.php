<?php

namespace Soarce\Action;

use Soarce\Action;
use Soarce\Config;

class End extends Action
{
    /**
     * @return string
     * @throws Exception
     */
    public function run(): string
    {
        if (!is_writable($this->config->getDataPath())) {
            throw new Exception('data dir does not exist, is not writable or full', Exception::DATA_DIRECTORY__NOT_WRITEABLE);
        }

        $this->writeKillfile();
        $this->deletePipes();

        return json_encode(['status' => 'ok']);
    }

    /**
     *
     */
    private function writeKillfile(): void
    {
        $file = $this->config->getDataPath() . DIRECTORY_SEPARATOR . Config::KILL_WORKER_FILENAME;
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * 
     */
    private function deletePipes(): void
    {
        for ($i = 0; $i < $this->config->getNumberOfPipes(); $i++) {
            $path = $this->config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, $i);
            unlink($path);
        }
    }
}
