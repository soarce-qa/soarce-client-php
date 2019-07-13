<?php

namespace Soarce;

abstract class Action
{
    /** @var Config */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param  string   $action
     * @param  string[] $params
     * @return string
     */
    public function url($action, $params = []): string
    {
        if ('' === trim($this->config->getActionParamName())) {
            throw new Exception('Empty action parameter name', Exception::FRONTCONTROLLER__NO_ACTION_PARAMETER_NAME);
        }

        if ('' === trim($action)) {
            throw new Exception('Empty action name', Exception::FRONTCONTROLLER__NO_ACTION_NAME);
        }

        $params = array_merge([$this->config->getActionParamName() => $action], $params);
        return '/?' . http_build_query($params);
    }


    /**
     * This has to become the concrete implementation of an action
     *
     * @return string
     */
    abstract public function run(): string;
}
