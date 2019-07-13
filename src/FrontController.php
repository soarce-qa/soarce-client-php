<?php

namespace Soarce;

use Soarce\Action\Dummy;

class FrontController
{
    /** @var string() */
    private $actionMap = [
        'dummy' => Dummy::class,
    ];

    public function run()
    {
        if (!isset($_GET['SOARCE'])) {
            return;
        }

        if (!isset($this->actionMap[$_GET['SOARCE']])) {
            return;
        }

        $classname = $this->actionMap[$_GET['SOARCE']];
        $action = new $classname();
        $action->run();
        die();
    }
}

(new FrontController())->run();