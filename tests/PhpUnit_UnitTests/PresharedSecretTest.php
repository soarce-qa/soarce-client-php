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

    public function setUp(): void
    {
        $this->storedServerParams = $_SERVER;
        $this->storedGetParams    = $_GET;
    }

    public function tearDown(): void
    {
        $_SERVER = $this->storedServerParams;
        $_GET    = $this->storedGetParams;
    }

    public function testNoConfigDoesNotBlock(): void
    {
        $_SERVER['HTTP_X_SOARCE_PRESHARED_SECRET'] = 'abcdefg';
        $_GET['SOARCE'] = 'index';
        $this->assertStringContainsString('Hello World!', (new FrontController(new Config()))->run());
    }

    public function testMatchingSecretDoesNotBlock(): void
    {
        $_SERVER['HTTP_X_SOARCE_PRESHARED_SECRET'] = 'abcdefg';
        $_GET['SOARCE'] = 'index';
        $_SERVER['SOARCE_PRESHARED_SECRET'] = 'abcdefg';
        $this->assertStringContainsString('Hello World!', (new FrontController(new Config()))->run());
    }

    public function testDifferentSecretsSkipSoarceExecution(): void
    {
        $_SERVER['HTTP_X_SOARCE_PRESHARED_SECRET'] = 'abcdefg';
        $_GET['SOARCE'] = 'index';
        $_SERVER['SOARCE_PRESHARED_SECRET'] = 'qwertyu';
        $this->assertEquals('', (new FrontController(new Config()))->run());
    }

}
