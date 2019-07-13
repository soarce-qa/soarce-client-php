<?php

namespace Soarce;

abstract class Action
{
    /**
     * @param  string   $action
     * @param  string[] $params
     * @return string
     */
    public static function url($action, $params = []): string
    {
        if ('' === trim(FrontController::getActionParamName())) {
            throw new Exception('Empty action parameter name', Exception::ACTION__NO_ACTION_PARAMETER_NAME);
        }

        if ('' === trim($action)) {
            throw new Exception('Empty action name', Exception::ACTION__NO_ACTION_NAME);
        }

        $params = array_merge([FrontController::getActionParamName() => $action], $params);
        return '/?' . http_build_query($params);
    }


    /**
     * This has to become the concrete implementation of an action
     *
     * @return string
     */
    abstract public function run(): string;
}
