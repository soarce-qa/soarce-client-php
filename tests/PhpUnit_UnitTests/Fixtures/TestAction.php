<?php

namespace UnitTests\Fixtures;

use Soarce\Action;

class TestAction extends Action
{

    public function run(): string
    {
        return 'something';
    }
}
