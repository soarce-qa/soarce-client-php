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
        if (!is_dir($this->config->getDataPath()) || !is_writable($this->config->getDataPath())) {
            throw new Exception('data dir does not exist, is not writable or full', Exception::DATA_DIRECTORY__NOT_WRITEABLE);
        }

        $this->createPipes();
        $this->removeKillfile();
        $this->startWorkerProcess();
        $this->createTriggerFile();

        return json_encode(['status' => 'OK']);
    }

    /**
     *
     */
    private function createPipes(): void
    {
        for ($i = 0; $i < $this->config->getNumberOfPipes(); $i++) {
            $path = $this->config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, $i) . '.' . Config::SUFFIX_TRACEFILE;
            exec("mkfifo {$path}");
        }
    }

    /**
     * creates a detached background process
     */
    private function startWorkerProcess(): void
    {
        exec('php -f '
            . __DIR__
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . 'worker.php '
            . $this->config->getDataPath()
            . ' '
            . $this->config->getNumberOfPipes()
            . ' >/dev/null 2>/dev/null &');
    }

    /**
     *
     */
    private function removeKillfile(): void
    {
        $file = $this->config->getDataPath() . DIRECTORY_SEPARATOR . Config::KILL_WORKER_FILENAME;
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     *
     */
    private function createTriggerFile(): void
    {
        $file = $this->config->getDataPath() . DIRECTORY_SEPARATOR . Config::TRIGGER_FILENAME;
        touch($file);
    }
}
