<?php

namespace Soarce;

class Config
{
    private const DEFAULT_ACTION_PARAM_NAME = 'SOARCE';
    private const DEFAULT_DATA_PATH         = '/tmp/';
    private const DEFAULT_NUMBER_OF_PIPES   = 10;

    public const COMPLETED_FILENAME   = '.SOARCE-completed';
    public const KILL_WORKER_FILENAME = '.SOARCE-kill-worker';
    public const PIPE_NAME_TEMPLATE   = 'SOARCE_PIPE_%d';
    public const TRIGGER_FILENAME     = '.SOARCE-gather-stats';
    public const SUFFIX_TRACEFILE     = '.xt';
    public const SUFFFIX_LOCK         = '.lock';

    /** @var string */
    protected $actionParamName;

    /** @var string */
    protected $dataPath;

    /** @var int */
    protected $numberOfPipes;

    /**
     * @return string
     */
    public function getActionParamName(): string
    {
        if (null === $this->actionParamName) {
            $this->actionParamName = $_ENV['SOARCE_ACTION_PARAM_NAME'] ?? $_SERVER['SOARCE_ACTION_PARAM_NAME'] ?? self::DEFAULT_ACTION_PARAM_NAME;
        }

        return $this->actionParamName;
    }

    /**
     * @param string $actionParamName
     */
    public function setActionParamName(string $actionParamName): void
    {
        $this->actionParamName = $actionParamName;
    }

    /**
     * @return string
     */
    public function getDataPath(): string
    {
        if (null === $this->dataPath) {
            $this->dataPath = $_ENV['SOARCE_DATA_PATH'] ?? $_SERVER['SOARCE_DATA_PATH'] ?? self::DEFAULT_DATA_PATH;
        }
        return $this->dataPath;
    }

    /**
     * @param string $dataPath
     */
    public function setDataPath(string $dataPath): void
    {
        $this->dataPath = $dataPath;
    }

    /**
     * @return bool
     */
    public function isTracingActive(): bool
    {
        return isset($_COOKIE['XDEBUG_TRACE'])
            || isset($_GET['XDEBUG_TRACE'])
            || isset($_POST['XDEBUG_TRACE'])
            || file_exists($this->getDataPath() . DIRECTORY_SEPARATOR . self::TRIGGER_FILENAME);
    }

    /**
     * @return int
     */
    public function getNumberOfPipes(): int
    {
        if (null === $this->numberOfPipes) {
            return self::DEFAULT_NUMBER_OF_PIPES;
        }
        return $this->numberOfPipes;
    }

    /**
     * @param int $numberOfPipes
     */
    public function setNumberOfPipes(int $numberOfPipes): void
    {
        $this->numberOfPipes = $numberOfPipes;
    }
}
