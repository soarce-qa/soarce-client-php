<?php

namespace UnitTests;

use PHPUnit\Framework\TestCase;
use Soarce\TraceParser;

class TraceParserTest extends TestCase
{
    public function testDemoFormat(): void
    {
        $parser = new TraceParser();
        $fp = fopen(__DIR__ . '/Fixtures/demo-trace-2.0.txt', 'rb');
        $parser->analyze($fp);

        $parsedData = $parser->getParsedData();
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

    public function testRealLogFromApp(): void
    {
        $parser = new TraceParser();
        $fp = fopen(__DIR__ . '/Fixtures/demo-trace-3.1-format-4.txt', 'rb');
        $parser->analyze($fp);

        $parsedData = $parser->getParsedData();

        $this->assertCount(4, $parsedData);
        $this->assertArrayHasKey('/var/www/html/invoices.php', $parsedData);
        $this->assertCount(3, $parsedData['/var/www/html/invoices.php']);
        $this->assertArrayHasKey('number_format', $parsedData['/var/www/html/invoices.php']);
        $this->assertArrayHasKey('count', $parsedData['/var/www/html/invoices.php']['number_format']);
        $this->assertEquals(48, $parsedData['/var/www/html/invoices.php']['number_format']['count']);
        $this->assertEquals(5, $parsedData['/var/www/html/invoices.php']['number_format']['number']);

        $this->assertEquals([
                2 => [
                    3 => 2,
                    4 => 2,
                    5 => 48,
                ],
                6 => [
                    7 => 1,
                ],
            ], $parser->getFunctionMap()
        );
    }
}