<?php

namespace UnitTests\Action;

use PHPUnit\Framework\TestCase;
use Soarce\Exception;
use Soarce\Action;
use Soarce\FrontController;

class HelperFunctionsTest extends TestCase
{
    public function setUp(): void
    {
        FrontController::setActionParamName('SOARCE');
    }

    public function testEmptyParamNameThrowsException(): void
    {
        FrontController::setActionParamName('');
        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::ACTION__NO_ACTION_PARAMETER_NAME);

        Action::url('something');
    }

    public function testEmptyActionThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::ACTION__NO_ACTION_NAME);

        Action::url('');
    }

    public function testUrlBuildWithEmptyParams(): void
    {
        $this->assertEquals(
            '/?SOARCE=index',
            Action::url('index')
        );
    }

    public function testUrlBuildWithParams(): void
    {
        $this->assertEquals(
            '/?SOARCE=index&blubb=blabb&blibb=blebb',
            Action::url('index', ['blubb' => 'blabb', 'blibb' => 'blebb'])
        );
    }
}
