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
        $this->markTestIncomplete('we have to treat lightly around starting processes!');

        $config = new Config();
        $config->setDataPath(__DIR__ . '/../../playground/');
        $config->setNumberOfPipes(2);

        $action = new Start($config);

        $return = $action->run();

        $this->assertJson($return);
        $this->assertEquals(['status' => 'OK'], json_decode($return, JSON_OBJECT_AS_ARRAY));

        $this->assertFileExists($config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, 0));
        $this->assertFileExists($config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, 1));
        $this->assertFileNotExists($config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, 2));

        $this->assertEquals(3, exec('ps aux | grep worker.php | wc -l'));

        unlink($config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, 0));
        unlink($config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, 1));
    }
}
