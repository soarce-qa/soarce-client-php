<?php

namespace Soarce;

class Config
{
    const DEFAULT_ACTION_PARAM_NAME    = 'SOARCE';
    const DEFAULT_DATA_PATH            = '/tmp/';
    const DEFAULT_NUMBER_OF_PIPES      = 10;
    public static $DEFAULT_WHITELISTED_HOST_IPS = array();
    public static $DEFAULT_WHITELISTED_PATHS    = array();
    const DEFAULT_PRESHARED_SECRET     = '';

    const PIPE_NAME_TEMPLATE   = 'SOARCE_PIPE_%d';
    const TRIGGER_FILENAME     = '.SOARCE-gather-stats';
    const SUFFIX_TRACEFILE     = '.xt';

    /** @var string */
    protected $actionParamName;

    /** @var string */
    protected $applicationName;

    /** @var string */
    protected $dataPath;

    /** @var int */
    protected $numberOfPipes;

    /** @var string[] */
    protected $whitelistedHostIps = array();

    /** @var string[] */
    protected $whitelistedPaths = array();

    /** @var string */
    protected $presharedSecret;

    /**
     * @return string
     */
    public function getPresharedSecret()
    {
        if (null === $this->presharedSecret) {
            if (isset($_ENV['SOARCE_PRESHARED_SECRET'])) {
                $this->presharedSecret = $_ENV['SOARCE_PRESHARED_SECRET'];
            } elseif (isset($_SERVER['SOARCE_PRESHARED_SECRET'])) {
                $this->presharedSecret = $_SERVER['SOARCE_PRESHARED_SECRET'];
            } else {
                $this->presharedSecret = self::DEFAULT_PRESHARED_SECRET;
            }
        }

        return $this->presharedSecret;
    }

    /**
     * @return string
     */
    public function getActionParamName()
    {
        if (null === $this->actionParamName) {
            if (isset($_ENV['SOARCE_ACTION_PARAM_NAME'])) {
                $this->actionParamName = $_ENV['SOARCE_ACTION_PARAM_NAME'];
            } elseif (isset($_SERVER['SOARCE_ACTION_PARAM_NAME'])) {
                $this->actionParamName = $_SERVER['SOARCE_ACTION_PARAM_NAME'];
            } else {
                $this->actionParamName = self::DEFAULT_ACTION_PARAM_NAME;
            }
        }

        return $this->actionParamName;
    }

    /**
     * @return string
     */
    public function getApplicationName()
    {
        if (null === $this->applicationName) {
            if (isset($_ENV['SOARCE_APPLICATION_NAME'])) {
                $this->applicationName = $_ENV['SOARCE_APPLICATION_NAME'];
            } elseif (isset($_SERVER['SOARCE_APPLICATION_NAME'])) {
                $this->applicationName = $_SERVER['SOARCE_APPLICATION_NAME'];
            } else {
                $this->applicationName = $_SERVER['HOSTNAME'];
            }
        }

        return $this->applicationName;
    }

    /**
     * @param string $actionParamName
     */
    public function setActionParamName($actionParamName)
    {
        $this->actionParamName = $actionParamName;
    }

    /**
     * @return string
     */
    public function getDataPath()
    {
        if (null === $this->dataPath) {
            if (isset($_ENV['SOARCE_DATA_PATH'])) {
                $this->dataPath = $_ENV['SOARCE_DATA_PATH'];
            } elseif (isset($_SERVER['SOARCE_DATA_PATH'])) {
                $this->dataPath = $_SERVER['SOARCE_DATA_PATH'];
            } else {
                $this->dataPath = self::DEFAULT_DATA_PATH;
            }
        }
        return $this->dataPath;
    }

    /**
     * @param string $dataPath
     */
    public function setDataPath($dataPath)
    {
        $this->dataPath = $dataPath;
    }

    /**
     * @return bool
     */
    public function isTracingActive()
    {
        return isset($_COOKIE['XDEBUG_TRACE'])
            || isset($_GET['XDEBUG_TRACE'])
            || isset($_POST['XDEBUG_TRACE'])
            || file_exists($this->getDataPath() . DIRECTORY_SEPARATOR . self::TRIGGER_FILENAME);
    }

    /**
     * @return int
     */
    public function getNumberOfPipes()
    {
        if (null === $this->numberOfPipes) {
            return self::DEFAULT_NUMBER_OF_PIPES;
        }
        return $this->numberOfPipes;
    }

    /**
     * @param int $numberOfPipes
     */
    public function setNumberOfPipes($numberOfPipes)
    {
        $this->numberOfPipes = $numberOfPipes;
    }

    /**
     * @return string[]
     */
    public function getWhitelistedHostIps()
    {
        if (array() === $this->whitelistedHostIps) {
            if (isset($_ENV['SOARCE_WHITELISTED_HOST_IPS']) && '' !== $_ENV['SOARCE_WHITELISTED_HOST_IPS']) {
                $this->whitelistedHostIps = explode(',', $_ENV['SOARCE_WHITELISTED_HOST_IPS']);
            } elseif (isset($_SERVER['SOARCE_WHITELISTED_HOST_IPS']) && $_SERVER['SOARCE_WHITELISTED_HOST_IPS']) {
                $this->whitelistedHostIps = explode(',', $_SERVER['SOARCE_WHITELISTED_HOST_IPS']);
            } else {
                $this->whitelistedHostIps = self::$DEFAULT_WHITELISTED_HOST_IPS;
            }
        }

        return $this->whitelistedHostIps;
    }

    /**
     * @param string[] $whitelistedHostIps
     */
    public function setWhitelistedHostIps(array $whitelistedHostIps)
    {
        $this->whitelistedHostIps = $whitelistedHostIps;
    }

    /**
     * @return string[]
     */
    public function getWhitelistedPaths()
    {
        if (array() === $this->whitelistedPaths) {
            if (isset($_ENV['SOARCE_WHITELISTED_PATHS']) && '' !== $_ENV['SOARCE_WHITELISTED_PATHS']) {
                $this->whitelistedPaths = explode(PATH_SEPARATOR, $_ENV['SOARCE_WHITELISTED_PATHS']);
            } elseif (isset($_SERVER['SOARCE_WHITELISTED_PATHS']) && $_SERVER['SOARCE_WHITELISTED_PATHS']) {
                $this->whitelistedPaths = explode(PATH_SEPARATOR, $_SERVER['SOARCE_WHITELISTED_PATHS']);
            } else {
                $this->whitelistedPaths = self::$DEFAULT_WHITELISTED_PATHS;
            }
        }

        return $this->whitelistedPaths;
    }

    /**
     * @param string[] $whitelistedPaths
     */
    public function setWhitelistedPaths(array $whitelistedPaths)
    {
        $this->whitelistedPaths = $whitelistedPaths;
    }
}
