<?php

namespace UnitTests;

use PHPUnit\Framework\TestCase;
use Soarce\Config;

class ConfigTest extends TestCase
{
    private array $storedGetParams;

    private array $storedEnvParams;

    private array $storedServerParams;

    public function setUp(): void
    {
        $this->storedGetParams    = $_GET;
        $this->storedEnvParams    = $_ENV;
        $this->storedServerParams = $_SERVER;
    }

    public function tearDown(): void
    {
        $_GET    = $this->storedGetParams;
        $_ENV    = $this->storedEnvParams;
        $_SERVER = $this->storedServerParams;
    }

    public function testDefaultParams(): void
    {
        $config = new Config();

        $this->assertEquals('SOARCE', $config->getActionParamName());
        $this->assertEquals('/tmp/',  $config->getDataPath());
    }

    public function testEnvParams(): void
    {
        $_ENV['SOARCE_ACTION_PARAM_NAME'] = 'HUUURZ';
        $_ENV['SOARCE_DATA_PATH']         = '/var/tmp/';

        $config = new Config();

        $this->assertEquals('HUUURZ',    $config->getActionParamName());
        $this->assertEquals('/var/tmp/', $config->getDataPath());
    }

    public function testServerParams(): void
    {
        $_SERVER['SOARCE_ACTION_PARAM_NAME'] = 'DUUUURRRR';
        $_SERVER['SOARCE_DATA_PATH']         = '/var/tmp/megapath/';

        $config = new Config();

        $this->assertEquals('DUUUURRRR',          $config->getActionParamName());
        $this->assertEquals('/var/tmp/megapath/', $config->getDataPath());
    }

    public function testEnvParamsTrumpServer(): void
    {
        $_ENV['SOARCE_ACTION_PARAM_NAME'] = 'HUUURZ';
        $_ENV['SOARCE_DATA_PATH']         = '/var/tmp/';

        $_SERVER['SOARCE_ACTION_PARAM_NAME'] = 'DUUUURRRR';
        $_SERVER['SOARCE_DATA_PATH']         = '/var/tmp/megapath/';

        $config = new Config();

        $this->assertEquals('HUUURZ',    $config->getActionParamName());
        $this->assertEquals('/var/tmp/', $config->getDataPath());
    }

    public function testOverrideTrumpDefaultParams(): void
    {
        $_ENV['SOARCE_ACTION_PARAM_NAME'] = 'HUUURZ';
        $_ENV['SOARCE_DATA_PATH']         = '/var/tmp/';

        $_SERVER['SOARCE_ACTION_PARAM_NAME'] = 'DUUUURRRR';
        $_SERVER['SOARCE_DATA_PATH']         = '/var/tmp/megapath/';

        $config = new Config();
        $config->setActionParamName('THISISAWESOME');
        $config->setDataPath('/this/is/private/');

        $this->assertEquals('THISISAWESOME',     $config->getActionParamName());
        $this->assertEquals('/this/is/private/', $config->getDataPath());
    }

    public function testIsTraceActive(): void
    {
        $config = new Config();
        $config->setDataPath(__DIR__ . '/../playground/');
        $this->assertFalse($config->isTracingActive());

        touch($config->getDataPath() . DIRECTORY_SEPARATOR . Config::TRIGGER_FILENAME);
        $this->assertTrue($config->isTracingActive());

        unlink($config->getDataPath() . DIRECTORY_SEPARATOR . Config::TRIGGER_FILENAME);
        $this->assertFalse($config->isTracingActive());

        $_GET['XDEBUG_TRACE'] = 1;
        $this->assertTrue($config->isTracingActive());
    }

    public function testNumberOfPipes(): void
    {
        $config = new Config();
        $this->assertEquals(10, $config->getNumberOfPipes());

        $config->setNumberOfPipes(42);
        $this->assertEquals(42, $config->getNumberOfPipes());
    }
}
