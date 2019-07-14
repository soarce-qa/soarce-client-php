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

    public function testReadingAnActualFile(): void
    {
        $config = new Config();
        $config->setDataPath(__DIR__ . '/../../playground/');

        $path = $config->getDataPath() . DIRECTORY_SEPARATOR . 'unittest.txt';
        file_put_contents($path, 'ThisIsSomethingToRead');

        $action = new ReadFile($config);
        $_GET['filename'] = $path;

        $this->assertEquals('ThisIsSomethingToRead', $action->run());

        unlink($path);
    }
}
