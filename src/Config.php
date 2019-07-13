<?php

namespace Soarce;

class Config
{
    /** @var string */
    protected $actionParamName;

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
}
