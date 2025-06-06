<?php

namespace Soarce;

abstract class Action
{
    public function __construct(protected Config $config)
    {
    }

    /**
     * This has to become the concrete implementation of an action
     *
     * @return string
     */
    abstract public function run(): string;
}
