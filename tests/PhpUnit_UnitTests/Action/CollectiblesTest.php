<?php

namespace UnitTests\Action;

use PHPUnit\Framework\TestCase;
use Soarce\Action\Collectibles;
use Soarce\Action\Exception;
use Soarce\Config;

class CollectiblesTest extends TestCase
{
    /** @var Config */
    private $config;

    public function setUp()
    {
        $this->config = new Config();
        $this->config->setDataPath(__DIR__ . '/../../playground/');
    }

    public function testUnreadableDataDirCausesException(): void
    {
        $this->config->setDataPath(__DIR__ . '/bwmuhahahahaaaaa/');

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::DATA_DIRECTORY__NOT_READABLE);

        $action = new Collectibles($this->config);
        $action->run();
    }

    public function testEmptyDataDirectoryYieldsEmptyResult(): void
    {
        $action = new Collectibles($this->config);
        $result = $action->run();

        $this->assertJson($result);
        $this->assertEquals([], json_decode($result, JSON_OBJECT_AS_ARRAY));
    }

    public function testEmptyUsecaseYieldsEmptyResult(): void
    {
        // create
        mkdir($this->config->getDataPath() . '/UnitTest');

        // run
        $action = new Collectibles($this->config);
        $result = $action->run();

        // assert
        $this->assertJson($result);
        $this->assertEquals([], json_decode($result, JSON_OBJECT_AS_ARRAY));

        // clean
        rmdir($this->config->getDataPath() . '/UnitTest');

    }

    public function testUsecaseWithoutCompletedFileYieldsEmptyResult(): void
    {
        // create
        mkdir($this->config->getDataPath() . '/UnitTest');
        touch($this->config->getDataPath() . '/UnitTest/some-request.xt');

        // run
        $action = new Collectibles($this->config);
        $result = $action->run();

        // assert
        $this->assertJson($result);
        $this->assertEquals([], json_decode($result, JSON_OBJECT_AS_ARRAY));

        // clean
        unlink($this->config->getDataPath() . '/UnitTest/some-request.xt');
        rmdir($this->config->getDataPath() . '/UnitTest');

    }

    public function testProperUsecaseYieldsResult(): void
    {
        // create
        mkdir($this->config->getDataPath() . '/UnitTest');
        touch($this->config->getDataPath() . '/UnitTest/some-request.xt');
        touch($this->config->getDataPath() . '/UnitTest/some-other-request.xt');
        touch($this->config->getDataPath() . '/UnitTest/' . Config::COMPLETED_FILENAME);

        // run
        $action = new Collectibles($this->config);
        $result = $action->run();

        // assert
        $this->assertJson($result);
        $decoded = json_decode($result, JSON_OBJECT_AS_ARRAY);

        $this->assertArrayHasKey('UnitTest', $decoded);
        $this->assertCount(2, $decoded['UnitTest']);
        
        // clean
        unlink($this->config->getDataPath() . '/UnitTest/' . Config::COMPLETED_FILENAME);
        unlink($this->config->getDataPath() . '/UnitTest/some-other-request.xt');
        unlink($this->config->getDataPath() . '/UnitTest/some-request.xt');
        rmdir($this->config->getDataPath() . '/UnitTest');
    }
}