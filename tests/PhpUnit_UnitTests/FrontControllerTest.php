<?php

namespace UnitTests;

use PHPUnit\Framework\TestCase;
use Soarce\Config;
use Soarce\FrontController;

class FrontControllerTest extends TestCase
{
    /** @var array */
    private $storedGetParams;

    public function setUp(): void
    {
        $this->storedGetParams = $_GET;
    }

    public function tearDown(): void
    {
        $_GET = $this->storedGetParams;
    }

    public function testParameterNotSetDoesNothing(): void
    {
        unset($_GET['SOARCE']);
        $this->assertEquals('', (new FrontController(new Config()))->run());
    }

    public function testNonexistantActionDoesNothing(): void
    {
        $_GET['SOARCE'] = 'hurrrglburrrgl';
        $this->assertEquals('', (new FrontController(new Config()))->run());
    }

    public function testIndexDoesSomething(): void
    {
        $_GET['SOARCE'] = 'index';
        $this->assertStringContainsString('Hello World!', (new FrontController(new Config()))->run());
    }

    public function testOverrideParamName(): void
    {
        $_GET['SECURITY'] = 'index';
        $config = new Config();
        $config->setActionParamName('SECURITY');

        $this->assertStringContainsString('Hello World!', (new FrontController($config))->run());
    }
}
