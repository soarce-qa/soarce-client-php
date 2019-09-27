<?php

namespace UnitTests;

use M6Web\Component\RedisMock\RedisMockFactory;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Predis\ClientInterface;
use Soarce\HashManager;

class HashManagerTest extends TestCase
{
    const REDIS_KEY = 'filehashes:testApplicationName';

    /** @var ClientInterface */
    private $redisMock;

    public function testStoreNewFileInEmptyCache()
    {
        $filename = realpath(__DIR__ . '/Fixtures/dummy.txt');

        $hashManager = $this->getPreparedInstance();
        $this->assertEquals(
            '54b0c58c7ce9f2a8b551351102ee0938',
            $hashManager->getMd5ForFile($filename)
        );

        $hashManager->save();

        $storageContent = $this->redisMock->hgetall(self::REDIS_KEY);
        $this->assertCount(1, $storageContent);
        $this->assertEquals('54b0c58c7ce9f2a8b551351102ee0938', array_pop($storageContent));
    }

    public function testCaching()
    {
        $filename = realpath(__DIR__ . '/Fixtures/dummy.txt');

        $hashManager = $this->getPreparedInstance();

        $this->redisMock->hset(self::REDIS_KEY, $filename, 'thisAintNoMd5Hash');

        $hashManager->load();

        $this->assertEquals(
            [$filename => 'thisAintNoMd5Hash'],
            $hashManager->getMd5ForFiles([$filename])
        );
    }

    public function testEvalIsIgnored()
    {
        $filename = "something in eval()'d code we do not want";

        $hashManager = $this->getPreparedInstance();
        $this->assertEquals(
            '',
            $hashManager->getMd5ForFile($filename)
        );
    }

    /**
     * @return HashManager
     */
    private function getPreparedInstance()
    {
        $factory   = new RedisMockFactory();

        /** @var ClientInterface $redisMock */
        $this->redisMock = $factory->getAdapter(Client::class, true);

        return new HashManager($this->redisMock, 'testApplicationName');
    }
}
