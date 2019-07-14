<?php

namespace UnitTests\Action;

use PHPUnit\Framework\TestCase;
use Soarce\Action\Exception;
use Soarce\Action\ReadFile;
use Soarce\Config;

class ReadFileTest extends TestCase
{
    public function tearDown()
    {
        unset($_GET['filename']);
    }

    public function testMissingParamCausesException(): void
    {
        $action = new ReadFile(new Config());

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::MISSING_FILENAME_PARAMETER);

        $action->run();
    }

    public function testUnreadableFileCausesException(): void
    {
        $action = new ReadFile(new Config());

        $_GET['filename'] = '/this/does/not/exist.xt';

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::FILE_NOT_FOUND);

        $action->run();
    }

/*
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
*/
}
