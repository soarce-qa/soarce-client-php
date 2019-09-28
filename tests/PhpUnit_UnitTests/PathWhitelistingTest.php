<?php

namespace UnitTests;

use PHPUnit\Framework\TestCase;
use Soarce\Action\Exception;
use Soarce\Config;
use Soarce\FrontController;

class PathWhitelistingTest extends TestCase
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

    public function testNoWhitelistDoesNotBlock()
    {
        $_GET['SOARCE'] = 'readfile';
        $_GET['filename'] = realpath(__DIR__ . '/Fixtures/dummy.txt');
        $_SERVER['SOARCE_WHITELISTED_PATHS'] = '';
        $this->assertContains('this is a test', (new FrontController(new Config()))->run());
    }

    public function testWhitelistAndPathWithinDoesNotBlock()
    {
        $_GET['SOARCE'] = 'readfile';
        $_GET['filename'] = realpath(__DIR__ . '/Fixtures/dummy.txt');
        $_SERVER['SOARCE_WHITELISTED_PATHS'] = '/home/:/something/else';
        $this->assertContains('this is a test', (new FrontController(new Config()))->run());
    }

    /**
     * @expectedException Exception
     * @expectedExceptionCode 5
     */
    public function testNonWhitelistedPathThrowsException()
    {
        $_GET['SOARCE'] = 'readfile';
        $_GET['filename'] = '/etc/passwd';
        $_SERVER['SOARCE_WHITELISTED_PATHS'] = '/home/:/something/else';
        (new FrontController(new Config()))->run();
    }
}
