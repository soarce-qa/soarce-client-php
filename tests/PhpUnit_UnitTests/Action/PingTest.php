<?php

namespace UnitTests\Action;

use PHPUnit\Framework\TestCase;
use Soarce\Action\Ping;
use Soarce\Config;

class PingTest extends TestCase
{
    public function testPongIsReceived()
    {
        $action = new Ping(new Config());
        $this->assertEquals('pong', $action->run());
    }
}
