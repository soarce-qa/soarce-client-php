<?php

namespace Soarce\Pipe;

use Soarce\Config;
use Soarce\Exception;
use Soarce\Pipe;
use Soarce\RedisMutex;

class Handler
{
    /**
     * Handler constructor.
     */
    public function __construct(private Config $config, private RedisMutex $redisMutex)
    {
    }

    /**
     * @return Pipe
     * @throws Exception
     */
    public function getFreePipe(): Pipe
    {
        for ($tries = 0; $tries < 5; $tries++) {
            $id = $this->redisMutex->obtainLock();
            if ($id >= 0 && $id < $this->config->getNumberOfPipes()) {
                return $this->getAllPipes()[$id];
            }
        }
        throw new Exception('cannot find unused pipe');
    }

    /**
     * @return Pipe[]
     */
    public function getAllPipes(): array
    {
        $pipes = [];
        for ($i = 0; $i < $this->config->getNumberOfPipes(); $i++) {
            $basePath = $this->config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, $i);
            $pipes[] = new Pipe($basePath);
        }
        return $pipes;
    }
}