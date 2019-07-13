<?php

namespace UnitTests\Action;

use PHPUnit\Framework\TestCase;
use Soarce\Config;
use Soarce\Exception;
use Soarce\Action;
use UnitTests\Fixtures\TestAction;

class HelperFunctionsTest extends TestCase
{
    public function testEmptyParamNameThrowsException(): void
    {
        $config = new Config();
        $config->setActionParamName('');

        $action = new TestAction($config);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::FRONTCONTROLLER__NO_ACTION_PARAMETER_NAME);

        $action->url('something');
    }

    public function testEmptyActionThrowsException(): void
    {
        $config = new Config();
        $config->setActionParamName('SUPERSOARCE');

        $action = new TestAction($config);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::FRONTCONTROLLER__NO_ACTION_NAME);

        $action->url('');
    }

    public function testUrlBuildWithEmptyParams(): void
    {
        $action = new TestAction(new Config());

        $this->assertEquals(
            '/?SOARCE=index',
            $action->url('index')
        );
    }

    public function testUrlBuildWithParams(): void
    {
        $action = new TestAction(new Config());

        $this->assertEquals(
            '/?SOARCE=index&blubb=blabb&blibb=blebb',
            $action->url('index', ['blubb' => 'blabb', 'blibb' => 'blebb'])
        );
    }
}
