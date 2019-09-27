<?php

namespace UnitTests;

use PHPUnit\Framework\TestCase;
use Soarce\TraceParser;

class TraceParserTest extends TestCase
{
    public function testEverything()
    {
        $parser = new TraceParser();
        $fp = fopen(__DIR__ . '/Fixtures/demo-trace.txt', 'rb');
        $parser->analyze($fp);

        $parsedData = $parser->getParsedData();
        $this->assertIsArray($parsedData);
        $this->assertCount(1, $parsedData);
        $this->assertArrayHasKey('../trace.php', $parsedData);
        $this->assertCount(4, $parsedData['../trace.php']);
        $this->assertArrayHasKey('ord', $parsedData['../trace.php']);
        $this->assertArrayHasKey('count', $parsedData['../trace.php']['ord']);
        $this->assertEquals(6, $parsedData['../trace.php']['ord']['count']);
        $this->assertEquals(3, $parsedData['../trace.php']['ord']['number']);

        $this->assertEquals([
                0 => [
                   1 => 1,
                   2 => 6,
                ],
                2 => [
                    3 => 6,
                ],
            ], $parser->getFunctionMap()
        );
    }
}