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
        if ('root' === $_SERVER['USER']) {
            $this->markTestSkipped('cannot test if run as root');
        }

        $config = new Config();
        $config->setDataPath('/root/.ssh');

        $action = new Start($config);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::DATA_DIRECTORY__NOT_WRITEABLE);

        $action->run();
    }

    public function testSuccessfulWrite(): void
    {
        $config = new Config();
        $config->setDataPath(__DIR__ . '/../../playground/');

        $action = new Start($config);

        $action->run();

        $this->assertFileExists(__DIR__ . '/../../playground/.SOARCE-gather-stats');
        unlink(__DIR__ . '/../../playground/.SOARCE-gather-stats');
    }
}
