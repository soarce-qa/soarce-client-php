<?php

namespace UnitTests;

use PHPUnit\Framework\TestCase;
use Soarce\Config;
use Soarce\FrontController;

class FrontControllerTest extends TestCase
{
    /** @var array */
    private $storedGetParams;

    public function setUp()
    {
        $this->storedGetParams = $_GET;
    }

    public function tearDown()
    {
        $_GET = $this->storedGetParams;
    }

    public function testParameterNotSetDoesNothing()
    {
        unset($_GET['SOARCE']);
        $this->assertEquals('', (new FrontController(new Config()))->run());
    }

    public function testNonexistantActionDoesNothing()
    {
        $_GET['SOARCE'] = 'hurrrglburrrgl';
        $this->assertEquals('', (new FrontController(new Config()))->run());
    }

    public function testIndexDoesSomething()
    {
        $_GET['SOARCE'] = 'index';
        $this->assertContains('Hello World!', (new FrontController(new Config()))->run());
    }

    public function testOverrideParamName()
    {
        $_GET['SECURITY'] = 'index';
        $config = new Config();
        $config->setActionParamName('SECURITY');

        $this->assertContains('Hello World!', (new FrontController($config))->run());
    }
}
