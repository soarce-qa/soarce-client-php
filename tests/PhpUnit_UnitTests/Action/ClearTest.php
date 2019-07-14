<?php

namespace UnitTests\Action;

use PHPUnit\Framework\TestCase;
use Soarce\Action\Clear;
use Soarce\Action\Exception;
use Soarce\Config;

class ClearTest extends TestCase
{
    /** @var Config */
    private $config;

    public function setUp()
    {
        $this->config = new Config();
        $this->config->setDataPath(__DIR__ . '/../../playground/');
    }

    public function tearDown()
    {
        unset($_GET['usecase']);
    }

    public function testUnwritableDataDirCausesException(): void
    {
        $this->config->setDataPath(__DIR__ . '/bwmuhahahahaaaaa/');

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::DATA_DIRECTORY__NOT_WRITEABLE);

        $action = new Clear($this->config);
        $action->run();
    }

    public function testMissingUsecaseNameCausesException(): void
    {
        $action = new Clear($this->config);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::NAME_OF_USECASE_MISSING_IN_REQUEST);

        $action->run();
    }

    public function testNonexistantUsecaseDirectoryDoesNotCauseException(): void
    {
        $action = new Clear($this->config);

        $_GET['usecase'] = 'humptyDumpty';

        $out = $action->run();
        $this->assertJson($out);

        $this->assertEquals(
            ['deleted_files' => 0],
            json_decode($out, JSON_OBJECT_AS_ARRAY)
        );
    }

    public function testRegularDeletion(): void
    {
        $action = new Clear($this->config);

        $_GET['usecase'] = 'UnitTest';

        mkdir($this->config->getDataPath() . '/UnitTest');
        touch($this->config->getDataPath() . '/UnitTest/some-request.xt');
        touch($this->config->getDataPath() . '/UnitTest/some-other-request.xt');
        touch($this->config->getDataPath() . '/UnitTest/' . Config::COMPLETED_FILENAME);

        $out = $action->run();
        $this->assertJson($out);

        $this->assertEquals(
            ['deleted_files' => 3],
            json_decode($out, JSON_OBJECT_AS_ARRAY)
        );

        $this->assertDirectoryNotExists($this->config->getDataPath() . '/UnitTest');
    }

}
