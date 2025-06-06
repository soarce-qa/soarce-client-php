<?php

namespace Soarce\Action;

use Predis\ClientInterface;
use Soarce\Action;
use Soarce\Config;
use Soarce\Pipe\Handler;
use Soarce\RedisMutex;

class Start extends Action implements PredisClientInterface
{
    private ClientInterface $predisClient;

    private RedisMutex $redisMutex;

    /**
     * @return string
     * @throws Exception
     */
    public function run(): string
    {
        $this->redisMutex = new RedisMutex($this->predisClient, $this->config->getApplicationName(), $this->config->getNumberOfPipes());

        if (!is_dir($this->config->getDataPath()) || !is_writable($this->config->getDataPath())) {
            throw new Exception('data dir does not exist, is not writable or full', Exception::DATA_DIRECTORY__NOT_WRITEABLE);
        }

        $this->createPipes();
        $this->seedRedisMutex();
        usleep(10000);
        $this->startWorkerProcess();
        $this->createTriggerFile();

        return json_encode(['status' => 'ok']);
    }

    /**
     * @param ClientInterface $client
     */
    public function setPredisClient(ClientInterface $client): void
    {
        $this->predisClient = $client;
    }

    /**
     *
     */
    private function seedRedisMutex(): void
    {
        $this->redisMutex->seed();
    }

    /**
     *
     */
    private function createPipes(): void
    {
        $pipeHandler = new Handler($this->config, $this->redisMutex);
        foreach ($pipeHandler->getAllPipes() as $pipe) {
            exec("mkfifo {$pipe->getFilenameTracefile()}");
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
            . 'workerMaster.php '
            . $this->config->getDataPath()
            . ' '
            . $this->config->getNumberOfPipes()
            . ' >/dev/null 2>/dev/null &');
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
