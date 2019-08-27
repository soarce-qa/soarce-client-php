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

    public function testNoWhitelistDoesNotBlock(): void
    {
        $_GET['SOARCE'] = 'readfile';
        $_GET['filename'] = realpath(__DIR__ . '/Fixtures/dummy.txt');
        $_SERVER['SOARCE_WHITELISTED_PATHS'] = '';
        $this->assertStringContainsString('this is a test', (new FrontController(new Config()))->run());
    }

    public function testWhitelistAndPathWithinDoesNotBlock(): void
    {
        $_GET['SOARCE'] = 'readfile';
        $_GET['filename'] = realpath(__DIR__ . '/Fixtures/dummy.txt');
        $_SERVER['SOARCE_WHITELISTED_PATHS'] = '/home/:/something/else';
        $this->assertStringContainsString('this is a test', (new FrontController(new Config()))->run());
    }

    public function testNonWhitelistedPathThrowsException(): void
    {
        $_GET['SOARCE'] = 'readfile';
        $_GET['filename'] = '/etc/passwd';
        $_SERVER['SOARCE_WHITELISTED_PATHS'] = '/home/:/something/else';
        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::FILE_NOT_FOUND);
        (new FrontController(new Config()))->run();
    }
}
