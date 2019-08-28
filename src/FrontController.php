<?php

namespace Soarce;

class FrontController
{
    /** @var Config */
    private $config;

    /** @var string() */
    private $actionMap = [
        'details'       => Action\Details::class,
        'end'           => Action\End::class,
        'index'         => Action\Index::class,
        'ping'          => Action\Ping::class,
        'preconditions' => Action\Preconditions::class,
        'readfile'      => Action\ReadFile::class,
        'start'         => Action\Start::class,
    ];

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * running or skipping SOARCE
     *
     * @return string
     */
    public function run(): string
    {
        if (!$this->isIpWhitelisted()) {
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
        return $action->run();
    }

    /**
     * @return bool
     */
    private function isIpWhitelisted(): bool
    {
        // no whitelist means we accept all calls (this is a dev tool and should not be hosted publicly anyways).
        if ($this->config->getWhitelistedHostIps() === []) {
            return true;
        }

        $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

        // not a remote call?
        if ('' === $ip) {
            return true;
        }

        return in_array($ip, $this->config->getWhitelistedHostIps(), true);
    }
}
