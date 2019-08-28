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
     * This has to become the concrete implementation of an action
     *
     * @return string
     */
    abstract public function run(): string;
}
