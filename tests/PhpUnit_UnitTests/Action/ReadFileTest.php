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

    /**
     * @expectedException Exception
     * @expectedExceptionCode 4
     */
    public function testMissingParamCausesException()
    {
        $action = new ReadFile(new Config());

        $action->run();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionCode 5
     */
    public function testUnreadableFileCausesException()
    {
        $action = new ReadFile(new Config());

        $_GET['filename'] = '/this/does/not/exist.xt';

        $action->run();
    }

    public function testReadingAnActualFile()
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
