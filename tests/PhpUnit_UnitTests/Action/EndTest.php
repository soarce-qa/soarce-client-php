<?php

namespace UnitTests\Action;

use PHPUnit\Framework\TestCase;
use Soarce\Action\Exception;
use Soarce\Action\End;
use Soarce\Config;

class EndTest extends TestCase
{
    /** @var Config */
    private $config;

    public function setUp()
    {
        $this->config = new Config();
        $this->config->setDataPath(__DIR__ . '/../../playground/');
        $_SERVER['HOSTNAME'] = 'UnitTest';
    }

    public function tearDown()
    {
        unset($_GET['usecase']);
    }

    public function testNonexistantDirectoryCausesException(): void
    {
        $this->config->setDataPath('/the/freaking/moon');

        $action = new End($this->config);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::DATA_DIRECTORY__NOT_WRITEABLE);

        $action->run();
    }

    public function testUnauthorizedDirectoryCausesException(): void
    {
        if ('root' === $_SERVER['USER']) {
            $this->markTestSkipped('cannot test if run as root');
        }

        $this->config->setDataPath('/root/.ssh');

        $action = new End($this->config);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::DATA_DIRECTORY__NOT_WRITEABLE);

        $action->run();
    }

    public function testProblemWithTraceDirectoryThrowsException(): void
    {
        $this->config->setDataPath('/warrrggbblllgrrglllblll/');

        $end = new End($this->config);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::DATA_DIRECTORY__NOT_WRITEABLE);

        $end->run();
    }

    public function testFreshDirectory(): void
    {
        $this->markTestIncomplete('starting processes is hard to test');

        // prepare
        touch($this->config->getDataPath() . DIRECTORY_SEPARATOR . Config::TRIGGER_FILENAME);

        // secure the stuff
        $end = new End($this->config);
        $out = $end->run();

        // assert
        $this->assertJson($out);
        $this->assertJsonStringEqualsJsonString('{"status": "ok"}', $out);

        $this->assertDirectoryExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest');
        $this->assertFileExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest/abcdef.xt');
        $this->assertFileExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest/abcdef.xt.coverage');

        // cleanup
        unlink($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest/abcdef.xt');
        unlink($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest/abcdef.xt.coverage');
        rmdir($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest');
    }

    public function testExistingDirectory(): void
    {
        $this->markTestIncomplete('starting processes is hard to test');

        // prepare
        touch(ini_get('xdebug.trace_output_dir') . DIRECTORY_SEPARATOR . 'abcdef.xt');
        touch(ini_get('xdebug.trace_output_dir') . DIRECTORY_SEPARATOR . 'abcdef.xt.coverage');
        touch($this->config->getDataPath() . DIRECTORY_SEPARATOR . Config::TRIGGER_FILENAME);
        mkdir($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2');
        touch($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/i_should_not_survive.xt');

        // secure the stuff
        $_GET['usecase'] = 'UnitTest2';
        $end = new End($this->config);
        $out = $end->run();

        // assert
        $this->assertJson($out);
        $this->assertJsonStringEqualsJsonString('{"files": 2}', $out);

        $this->assertDirectoryExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2');
        $this->assertFileExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/abcdef.xt');
        $this->assertFileExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/abcdef.xt.coverage');
        $this->assertFileNotExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/i_should_not_survive.xt');

        // cleanup
        unlink($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/abcdef.xt');
        unlink($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/abcdef.xt.coverage');
        rmdir($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2');
    }
}
