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
        $fc = new FrontController(new Config());
        $this->assertEquals('', $fc->run());
    }

    public function testNonexistantActionDoesNothing()
    {
        $_GET['SOARCE'] = 'hurrrglburrrgl';
        $fc = new FrontController(new Config());
        $this->assertEquals('', $fc->run());
    }

    public function testIndexDoesSomething()
    {
        $_GET['SOARCE'] = 'index';
        $fc = new FrontController(new Config());
        $this->assertContains('Hello World!', $fc->run());
    }

    public function testOverrideParamName()
    {
        $_GET['SECURITY'] = 'index';
        $config = new Config();
        $config->setActionParamName('SECURITY');

        $fc = new FrontController($config);

        $this->assertContains('Hello World!', $fc->run());
    }
}
