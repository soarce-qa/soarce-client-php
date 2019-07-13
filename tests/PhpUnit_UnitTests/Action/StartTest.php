<?php

namespace UnitTests\Action;

use PHPUnit\Framework\TestCase;
use Soarce\Action\Exception;
use Soarce\Action\Start;
use Soarce\Config;

class StartTest extends TestCase
{
    public function testNonexistantDirectoryCausesException(): void
    {
        $config = new Config();
        $config->setDataPath('/the/freaking/moon');

        $action = new Start($config);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::DATA_DIRECTORY__NOT_WRITEABLE);

        $action->run();
    }

    public function testUnauthorizedDirectoryCausesException(): void
    {
        $config = new Config();
        $config->setDataPath('/bin/');

        $action = new Start($config);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::DATA_DIRECTORY__NOT_WRITEABLE);

        $action->run();
    }

    public function testSuccessfulWrite(): void
    {
        $config = new Config();
        $config->setDataPath('/tmp/');

        $action = new Start($config);

        $action->run();

        $this->assertFileExists('/tmp/.SOARCE-gather-stats');
        unlink('/tmp/.SOARCE-gather-stats');
    }
}
