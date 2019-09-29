<?php

namespace UnitTests;

use PHPUnit\Framework\TestCase;
use Soarce\Config;
use Soarce\FrontController;

class PresharedSecretTest extends TestCase
{
    /** @var array */
    private $storedServerParams;

    /** @var array */
    private $storedGetParams;

    public function setUp()
    {
        $this->storedServerParams = $_SERVER;
        $this->storedGetParams    = $_GET;
    }

    public function tearDown()
    {
        $_SERVER = $this->storedServerParams;
        $_GET    = $this->storedGetParams;
    }

    public function testNoConfigDoesNotBlock()
    {
        $_SERVER['HTTP_X_SOARCE_PRESHARED_SECRET'] = 'abcdefg';
        $_GET['SOARCE'] = 'index';
        $fc = new FrontController(new Config());
        $this->assertContains('Hello World!', $fc->run());
    }

    public function testMatchingSecretDoesNotBlock()
    {
        $_SERVER['HTTP_X_SOARCE_PRESHARED_SECRET'] = 'abcdefg';
        $_GET['SOARCE'] = 'index';
        $_SERVER['SOARCE_PRESHARED_SECRET'] = 'abcdefg';
        $fc = new FrontController(new Config());
        $this->assertContains('Hello World!', $fc->run());
    }

    public function testDifferentSecretsSkipSoarceExecution()
    {
        $_SERVER['HTTP_X_SOARCE_PRESHARED_SECRET'] = 'abcdefg';
        $_GET['SOARCE'] = 'index';
        $_SERVER['SOARCE_PRESHARED_SECRET'] = 'qwertyu';
        $fc = new FrontController(new Config());
        $this->assertEquals('', $fc->run());
    }

}
