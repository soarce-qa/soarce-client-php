<?php

namespace UnitTests;

use PHPUnit\Framework\TestCase;
use Soarce\Pipe;

class PipeTest extends TestCase
{
    public function testStatics(): void
    {
        $pipe = new Pipe('/tmp/SOARCE-TEST');
        $this->assertEquals('/tmp/SOARCE-TEST',      $pipe->getBasepath());
        $this->assertEquals('/tmp/SOARCE-TEST.lock', $pipe->getFilenameLock());
        $this->assertEquals('/tmp/SOARCE-TEST.xt',   $pipe->getFilenameTracefile());
    }

    public function testReleaseFunction(): void
    {
        $interceptor = false;
        $pipe = new Pipe('/tmp/SOARCE-TEST');
        $pipe->registerReleaseFunction(static function() use (&$interceptor) {
           $interceptor = true;
        });

        unset($pipe);

        $this->assertTrue($interceptor);
    }
}