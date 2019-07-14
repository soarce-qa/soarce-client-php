<?php

namespace Soarce;

class FrontController
{
    /** @var Config */
    private $config;

    /** @var string() */
    private $actionMap = [
        'collectibles'  => Action\Collectibles::class,
        'end'           => Action\End::class,
        'index'         => Action\Index::class,
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
}
