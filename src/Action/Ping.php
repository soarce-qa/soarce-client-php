<?php

namespace Soarce\Action;

use Soarce\Action;

class Ping extends Action
{
    /**
     * @return string
     */
    public function run()
    {
        return 'pong';
    }
}
