<?php

namespace UnitTests\Action;

use PHPUnit\Framework\TestCase;
use Soarce\Action\Preconditions;
use Soarce\Config;

class PreconditionsTest extends TestCase
{
    public function testEverythingIsWell()
    {
        $config = new Config();
        $config->setDataPath(__DIR__ . '/../../playground/');

        $action = new Preconditions($config);
        $return = $action->run();

        $this->assertJson($return);
        $decoded = json_decode($return, JSON_OBJECT_AS_ARRAY);

        $this->assertEquals(
            array(
                'xdebug_installed'      => true,
                'autotrace_off'         => true,
                'trace_format'          => true,
                'trace_output_name'     => true,
                'trace_trigger_enabled' => true,
                'tracedir_readable'     => true,
                'datadir_writable'      => true,
            ),
            $decoded
        );
    }
}
