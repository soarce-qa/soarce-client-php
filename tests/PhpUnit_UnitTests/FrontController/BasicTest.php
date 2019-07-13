<?php

namespace UnitTests\FrontController;

use PHPUnit\Framework\TestCase;
use Soarce\Config;
use Soarce\FrontController;

class BasicTest extends TestCase
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
        $this->assertStringContainsString('/?SOARCE=preconditions', (new FrontController(new Config()))->run());
    }
}
