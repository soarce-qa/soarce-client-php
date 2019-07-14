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

    /** @var string */
    private $xdebugTraceDirectory = '';

    public function setUp()
    {
        $this->config = new Config();
        $this->config->setDataPath(__DIR__ . '/../../playground/');
        if ('' === ini_get('xdebug.trace_output_dir')) {
            ini_set('xdebug.trace_output_dir', '/tmp/');
        }
        $this->xdebugTraceDirectory = ini_get('xdebug.trace_output_dir');
    }

    public function tearDown()
    {
        ini_set('xdebug.trace_output_dir', $this->xdebugTraceDirectory);
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

    public function testMissingUsecaseNameCausesException(): void
    {
        $action = new End($this->config);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::NAME_OF_USECASE_MISSING_IN_REQUEST);

        $action->run();
    }

    public function testInvalidUsecaseNameCausesException(): void
    {
        $action = new End($this->config);

        $_GET['usecase'] = '@~#{}[]$%&*';

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::NAME_OF_USECASE_MISSING_IN_REQUEST);

        $action->run();
    }

    public function testProblemWithTraceDirectoryThrowsException(): void
    {
        ini_set('xdebug.trace_output_dir', '/warrrggbblllgrrglllblll/');

        $_GET['usecase'] = 'UnitTest';
        $end = new End($this->config);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::TRACEFILE_DIRECTORY_NOT_READABLE);

        $end->run();
    }

    public function testFreshDirectory(): void
    {
        // prepare
        touch(ini_get('xdebug.trace_output_dir') . DIRECTORY_SEPARATOR . 'abcdef.xt');
        touch(ini_get('xdebug.trace_output_dir') . DIRECTORY_SEPARATOR . 'abcdef.xt.coverage');
        touch($this->config->getDataPath() . DIRECTORY_SEPARATOR . Config::TRIGGER_FILENAME);

        // secure the stuff
        $_GET['usecase'] = 'UnitTest';
        $end = new End($this->config);
        $end->run();

        // assert
        $this->assertDirectoryExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest');
        $this->assertFileExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest/abcdef.xt');
        $this->assertFileExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest/abcdef.xt.coverage');
        $this->assertFileExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest/' . Config::COMPLETED_FILENAME);

        // cleanup
        unlink($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest/abcdef.xt');
        unlink($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest/abcdef.xt.coverage');
        unlink($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest/' . Config::COMPLETED_FILENAME);
        rmdir($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest');
    }

    public function testExistingDirectory(): void
    {
        // prepare
        touch(ini_get('xdebug.trace_output_dir') . DIRECTORY_SEPARATOR . 'abcdef.xt');
        touch(ini_get('xdebug.trace_output_dir') . DIRECTORY_SEPARATOR . 'abcdef.xt.coverage');
        touch($this->config->getDataPath() . DIRECTORY_SEPARATOR . Config::TRIGGER_FILENAME);
        mkdir($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2');
        touch($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/i_should_not_survive.xt');

        // secure the stuff
        $_GET['usecase'] = 'UnitTest2';
        $end = new End($this->config);
        $end->run();

        // assert
        $this->assertDirectoryExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2');
        $this->assertFileExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/abcdef.xt');
        $this->assertFileExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/abcdef.xt.coverage');
        $this->assertFileExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/' . Config::COMPLETED_FILENAME);
        $this->assertFileNotExists($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/i_should_not_survive.xt');

        // cleanup
        unlink($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/abcdef.xt');
        unlink($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/abcdef.xt.coverage');
        unlink($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2/' . Config::COMPLETED_FILENAME);
        rmdir($this->config->getDataPath() . DIRECTORY_SEPARATOR . 'UnitTest2');
    }
}
