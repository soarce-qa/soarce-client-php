<?php

namespace Soarce\Pipe;

use Soarce\Config;
use Soarce\Exception;
use Soarce\Pipe;
use Soarce\RedisMutex;

class Handler
{
    /** @var Config */
    private $config;

    /** @var RedisMutex */
    private $redisMutex;

    /**
     * Handler constructor.
     *
     * @param Config     $config
     * @param RedisMutex $redisMutex
     */
    public function __construct(Config $config, RedisMutex $redisMutex)
    {
        $this->config = $config;
        $this->redisMutex = $redisMutex;
    }

    /**
     * @return Pipe
     * @throws Exception
     */
    public function getFreePipe()
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
    public function getAllPipes()
    {
        $pipes = [];
        for ($i = 0; $i < $this->config->getNumberOfPipes(); $i++) {
            $basePath = $this->config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, $i);
            $pipes[] = new Pipe($basePath);
        }
        return $pipes;
    }
}