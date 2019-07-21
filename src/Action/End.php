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

        $this->deleteTriggerFile();
        $this->deletePipes();
        $this->killWorker();

        return json_encode(['status' => 'ok']);
    }

    /**
     * @return void
     */
    private function deletePipes(): void
    {
        for ($i = 0; $i < $this->config->getNumberOfPipes(); $i++) {
            $path = $this->config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, $i) . '.' . Config::SUFFIX_TRACEFILE;
            unlink($path);
        }
    }

    /**
     * @return void
     */
    private function deleteTriggerFile(): void
    {
        $path = $this->config->getDataPath() . DIRECTORY_SEPARATOR . Config::TRIGGER_FILENAME;
        unlink($path);
    }

    /**
     * Kills the worker hard
     *
     * @return void
     */
    private function killWorker(): void
    {
        $pidFile = $this->config->getDataPath() . DIRECTORY_SEPARATOR . 'worker.pid';
        if (!file_exists($pidFile)) {
            return;
        }

        $pid = file_get_contents($pidFile);
        exec('kill -9 ' . $pid);
    }
}
