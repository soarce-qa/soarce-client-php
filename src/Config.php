<?php

namespace Soarce;

class Config
{
    public const TRIGGER_FILENAME    = '.SOARCE-gather-stats';
    public const COMPLETED_FILENAME  = '.SOARCE-completed';
    public const SUFFIX_TRACEFILE    = 'xt';
    public const SUFFIX_COVERAGEFILE = 'coverage';

    /** @var string */
    protected $actionParamName;

    /** @var string */
    protected $dataPath;

    /**
     * @return string
     */
    public function getActionParamName(): string
    {
        if (null === $this->actionParamName) {
            $this->actionParamName = $_ENV['SOARCE_ACTION_PARAM_NAME'] ?? $_SERVER['SOARCE_ACTION_PARAM_NAME'] ?? 'SOARCE';
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
            $this->dataPath = $_ENV['SOARCE_DATA_PATH'] ?? $_SERVER['SOARCE_DATA_PATH'] ?? '/tmp/';
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

    public function isTracingActive()
    {
        return isset($_COOKIE['XDEBUG_TRACE'])
            || isset($_GET['XDEBUG_TRACE'])
            || isset($_POST['XDEBUG_TRACE'])
            || file_exists($this->getDataPath() . DIRECTORY_SEPARATOR . self::TRIGGER_FILENAME);
    }
}
