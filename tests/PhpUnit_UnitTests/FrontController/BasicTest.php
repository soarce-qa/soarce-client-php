<?php

namespace UnitTests\FrontController;

use PHPUnit\Framework\TestCase;
use Soarce\Exception;
use Soarce\Action;
use Soarce\FrontController;

class BasicTest extends TestCase
{
    /** @var array */
    private $storedGetParams;

    public function setUp(): void
    {
        FrontController::setActionParamName('SOARCE');
        $this->storedGetParams = $_GET;
    }

    public function tearDown(): void
    {
        $_GET = $this->storedGetParams;
    }

    public function testParameterNotSetDoesNothing(): void
    {
        unset($_GET['SOARCE']);
        $this->assertEquals('', (new FrontController())->run());
    }

    public function testNonexistantActionDoesNothing(): void
    {
        $_GET['SOARCE'] = 'hurrrglburrrgl';
        $this->assertEquals('', (new FrontController())->run());
    }

    public function testIndexDoesSomething(): void
    {
        $_GET['SOARCE'] = 'index';
        $this->assertNotEquals('', (new FrontController())->run());
    }
}
