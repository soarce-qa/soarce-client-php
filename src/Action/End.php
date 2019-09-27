<?php

namespace Soarce\Action;

use Predis\ClientInterface;
use Soarce\Action;
use Soarce\Config;
use Soarce\Pipe\Handler;
use Soarce\RedisMutex;

class End extends Action implements PredisClientInterface
{
    /** @var ClientInterface */
    private $predisClient;

    /** @var RedisMutex */
    private $redisMutex;

    /**
     * @return string
     * @throws Exception
     */
    public function run(): string
    {
        $this->redisMutex = new RedisMutex($this->predisClient, $this->config->getApplicationName(), $this->config->getNumberOfPipes());

        if (!is_writable($this->config->getDataPath())) {
            throw new Exception('data dir does not exist, is not writable or full', Exception::DATA_DIRECTORY__NOT_WRITEABLE);
        }

        $this->deleteTriggerFile();
        $this->killWorker();
        $this->deletePipes();
        $this->cleanRedisMutex();

        return json_encode(['status' => 'ok']);
    }

    /**
     * @param ClientInterface $client
     */
    public function setPredisClient(ClientInterface $client)
    {
        $this->predisClient = $client;
    }

    /**
     *
     */
    private function cleanRedisMutex()
    {
        $this->redisMutex->clean();
    }

    /**
     * @return void
     */
    private function deletePipes()
    {
        $pipeHandler = new Handler($this->config, $this->redisMutex);
        foreach ($pipeHandler->getAllPipes() as $pipe) {
            if (file_exists($pipe->getFilenameTracefile())) {
                unlink($pipe->getFilenameTracefile());
            }
        }
    }

    /**
     * @return void
     */
    private function deleteTriggerFile()
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
    private function killWorker()
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
