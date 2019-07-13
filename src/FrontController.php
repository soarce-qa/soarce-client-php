<?php

namespace Soarce;

class FrontController
{
    /** @var string */
    protected static $actionParamName = 'SOARCE';

    /** @var string() */
    private $actionMap = [
        'index' => Action\Index::class,
    ];

    /**
     * @return string
     */
    public static function getActionParamName(): string
    {
        return self::$actionParamName;
    }

    /**
     * @param string $actionParamName
     */
    public static function setActionParamName(string $actionParamName): void
    {
        self::$actionParamName = $actionParamName;
    }

    /**
     * running or skipping SOARCE
     *
     * @return string
     */
    public function run(): string
    {
        if (!isset($_GET[self::$actionParamName])) {
            return '';
        }

        if (!isset($this->actionMap[$_GET[self::$actionParamName]])) {
            return '';
        }

        $classname = $this->actionMap[$_GET[self::$actionParamName]];

        /** @var Action $action */
        $action = new $classname();
        return $action->run();
    }
}
