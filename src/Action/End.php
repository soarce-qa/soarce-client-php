<?php

namespace Soarce\Action;

use Soarce\Action;
use Soarce\Config;
use Soarce\Pipe\Handler;

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
        $this->killWorker();
        $this->deletePipes();

        return json_encode(['status' => 'ok']);
    }

    /**
     * @return void
     */
    private function deletePipes(): void
    {
        $pipeHandler = new Handler($this->config);
        foreach ($pipeHandler->getAllPipes() as $pipe) {
            if (file_exists($pipe->getFilenameTracefile())) {
                unlink($pipe->getFilenameTracefile());
            }

            if (file_exists($pipe->getFilenameLock())) {
                unlink($pipe->getFilenameLock());
            }
        }
    }

    /**
     * @return void
     */
    private function deleteTriggerFile(): void
    {
        $path = $this->config->getDataPath() . DIRECTORY_SEPARATOR . Config::TRIGGER_FILENAME;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Kills the worker hard
     *
     * @return void
     */
    private function killWorker(): void
    {
        $pidFile = $this->config->getDataPath() . DIRECTORY_SEPARATOR . 'worker.pid';
        if (file_exists($pidFile)) {
            $pid = file_get_contents($pidFile);
            exec('kill -9 ' . $pid);
            unlink($pidFile);
        }

        for ($i = 0; $i < $this->config->getNumberOfPipes(); $i++) {
            $pidFile = $this->config->getDataPath() . DIRECTORY_SEPARATOR . 'worker-' . $i . '.pid';
            if (file_exists($pidFile)) {
                $pid = file_get_contents($pidFile);
                exec('kill -9 ' . $pid);
                unlink($pidFile);
            }
        }
    }
}
