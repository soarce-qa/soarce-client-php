<?php

namespace UnitTests;

use PHPUnit\Framework\TestCase;
use Soarce\Config;
use Soarce\FrontController;

class IpWhitelistingTest extends TestCase
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

    public function testNoIpDoesNotBlock()
    {
        unset($_SERVER['HTTP_CLIENT_IP'], $_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['REMOTE_ADDR']);
        $_GET['SOARCE'] = 'index';
        $_SERVER['SOARCE_WHITELISTED_HOST_IPS'] = '::1,127.0.0.1';
        $fc = new FrontController(new Config());
        $this->assertContains('Hello World!', $fc->run());
    }

    public function testNoWhitelistDoesNotBlock()
    {
        $_SERVER['REMOTE_ADDR'] = '::1';
        $_GET['SOARCE'] = 'index';
        $_SERVER['SOARCE_WHITELISTED_HOST_IPS'] = '';
        $fc = new FrontController(new Config());
        $this->assertContains('Hello World!', $fc->run());
    }

    public function testWhitelistAndCorrectIpDoesNotBlock()
    {
        $_SERVER['REMOTE_ADDR'] = '::1';
        $_GET['SOARCE'] = 'index';
        $_SERVER['SOARCE_WHITELISTED_HOST_IPS'] = '127.0.0.1,::1,192.168.0.2';
        $fc = new FrontController(new Config());
        $this->assertContains('Hello World!', $fc->run());
    }

    public function testNonWhitelistedIpSkipsSoarceExecution()
    {
        $_SERVER['REMOTE_ADDR'] = '42::1';
        $_GET['SOARCE'] = 'index';
        $_SERVER['SOARCE_WHITELISTED_HOST_IPS'] = '127.0.0.1,::1,192.168.0.2';
        $fc = new FrontController(new Config());
        $this->assertEquals('', $fc->run());
    }

}
