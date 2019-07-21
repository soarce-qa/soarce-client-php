<?php

namespace Soarce\Pipe;

use Soarce\Config;
use Soarce\Exception;
use Soarce\Pipe;

class Handler
{
    /** @var Config */
    private $config;

    /**
     * Handler constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return Pipe
     * @throws Exception
     */
    public function getFreePipe(): Pipe
    {
        for ($tries = 0; $tries < 20; $tries++) {
            foreach ($this->getAllPipes() as $pipe) {
                if (file_exists($pipe->getFilenameLock())) {
                    $fp = fopen($pipe->getFilenameLock(), 'wb');
                    if (flock($fp, LOCK_EX | LOCK_NB)) {
                        fwrite($fp, getmypid());
                        return $pipe;
                    }
                    continue;
                }

                $fp = fopen($pipe->getFilenameLock(), 'wb');
                flock($fp, LOCK_EX);
                fwrite($fp, getmypid());
                return $pipe;
            }
            usleep(10000);
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