<?php

namespace UnitTests\Action;

use PHPUnit\Framework\TestCase;
use Soarce\Action\Preconditions;
use Soarce\Config;

class PreconditionsTest extends TestCase
{
    public function testEverythingIsWell(): void
    {
        $config = new Config();
        $config->setDataPath(__DIR__ . '/../../playground/');

        $action = new Preconditions($config);
        $return = $action->run();

        $this->assertJson($return);
        $decoded = json_decode($return, JSON_OBJECT_AS_ARRAY);

        $this->assertEquals(
            [
                'xdebug_installed'   => true,
                'xdebug_3'           => true,
                'compression_off'    => true,
                'trace_format'       => true,
                'trace_output_name'  => true,
                'datadir_writable'   => true,
                'outputdir_readable' => true,
                'xdebug_mode'        => true,
            ],
            $decoded
        );
    }
}
