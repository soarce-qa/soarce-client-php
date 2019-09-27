<?php

namespace UnitTests\Action;

use M6Web\Component\RedisMock\RedisMockFactory;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Predis\ClientInterface;
use Soarce\Action\Exception;
use Soarce\Action\Start;
use Soarce\Config;

class StartTest extends TestCase
{
    /**
     * @expectedException Exception
     * @expectedExceptionCode 1
     */
    public function testNonexistantDirectoryCausesException()
    {
        $config = new Config();
        $config->setDataPath('/the/freaking/moon');

        $action = new Start($config);
        $action->setPredisClient($this->getRedisMock());

        $action->run();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionCode 1
     */
    public function testUnauthorizedDirectoryCausesException()
    {
        if ('root' === $_SERVER['USER']) {
            $this->markTestSkipped('cannot test if run as root');
        }

        $config = new Config();
        $config->setDataPath('/root/.ssh');

        $action = new Start($config);
        $action->setPredisClient($this->getRedisMock());

        $action->run();
    }

    public function testSuccessfulWrite()
    {
        $this->markTestIncomplete('we have to treat lightly around starting processes!');

        $config = new Config();
        $config->setDataPath(__DIR__ . '/../../playground/');
        $config->setNumberOfPipes(2);

        $action = new Start($config);
        $action->setPredisClient($this->getRedisMock());

        $return = $action->run();

        $this->assertJson($return);
        $this->assertEquals(['status' => 'OK'], json_decode($return, JSON_OBJECT_AS_ARRAY));

        $this->assertFileExists($config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, 0));
        $this->assertFileExists($config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, 1));
        $this->assertFileNotExists($config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, 2));

        $this->assertEquals(3, exec('ps aux | grep worker.php | wc -l'));

        unlink($config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, 0));
        unlink($config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, 1));
    }

    /**
     * @return ClientInterface
     */
    private function getRedisMock()
    {
        $factory = new RedisMockFactory();
        /** @var ClientInterface $redisMock */
        $redisMock = $factory->getAdapter(Client::class, true);

        return $redisMock;
    }
}
