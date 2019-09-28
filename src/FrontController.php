<?php

namespace Soarce;

use Predis\Client;
use Soarce\Action\PredisClientInterface;

class FrontController
{
    /** @var Config */
    private $config;

    /** @var string() */
    private $actionMap = array(
        'details'       => '\Soarce\Action\Details',
        'end'           => '\Soarce\Action\End',
        'index'         => '\Soarce\Action\Index',
        'ping'          => '\Soarce\Action\Ping',
        'preconditions' => '\Soarce\Action\Preconditions',
        'readfile'      => '\Soarce\Action\ReadFile',
        'start'         => '\Soarce\Action\Start',
    );

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * running or skipping SOARCE
     *
     * @return string
     */
    public function run()
    {
        if (!$this->isIpWhitelisted() || !$this->isPresharedSecretAuthorized()) {
            return '';
        }

        $actionParamName = $this->config->getActionParamName();
        if (!isset($_GET[$actionParamName])) {
            return '';
        }

        if (!isset($this->actionMap[$_GET[$actionParamName]])) {
            return '';
        }

        $classname = $this->actionMap[$_GET[$actionParamName]];

        /** @var Action $action */
        $action = new $classname($this->config);

        if ($action instanceof PredisClientInterface) {
            $predisClient = new Client(array(
                'scheme' => 'tcp',
                'host'   => 'soarce.local',
                'port'   => 6379,
            ));
            $action->setPredisClient($predisClient);
        }

        return $action->run();
    }

    /**
     * @return bool
     */
    private function isPresharedSecretAuthorized()
    {
        if ($this->config->getPresharedSecret() === '') {
            return true;
        }

        if (! isset($_SERVER['HTTP_X_SOARCE_PRESHARED_SECRET'])) {
            return false;
        }

        if ($_SERVER['HTTP_X_SOARCE_PRESHARED_SECRET'] !== $this->config->getPresharedSecret()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function isIpWhitelisted()
    {
        // no whitelist means we accept all calls (this is a dev tool and should not be hosted publicly anyways).
        if ($this->config->getWhitelistedHostIps() === array()) {
            return true;
        }

        $ip = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // not a remote call?
        if ('' === $ip) {
            return true;
        }

        return in_array($ip, $this->config->getWhitelistedHostIps(), true);
    }
}
