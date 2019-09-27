<?php

namespace UnitTests;

use M6Web\Component\RedisMock\RedisMockFactory;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Predis\ClientInterface;
use Soarce\RequestTracking;

class RequestTrackingTest extends TestCase
{
    const HEADER_NAME = 'HTTP_X_SOARCE_REQUEST_ID';

    /** @var array */
    private $storedServerParams;

    public function setUp()
    {
        $this->storedServerParams = $_SERVER;
        $_SERVER['SERVER_ADDR'] = '1.3.3.7';
        $_SERVER['REMOTE_ADDR'] = '42.42.42.42';
    }

    public function tearDown()
    {
        $_SERVER = $this->storedServerParams;
    }

    public function testIdFromRequestHeader()
    {
        $this->markTestIncomplete('the mock is faulty - we will make this into an integration test at a later point in time.');

        $_SERVER[self::HEADER_NAME] = 'abcdefghijkl';

        $redis = $this->getRedisMock();

        $requestTracking = new RequestTracking($redis);
        $this->assertEquals('abcdefghijkl', $requestTracking->getRequestId());

        $this->assertCount(1, $redis->keys('request:1.3.3.7'));
        $this->assertEquals(['abcdefghijkl'], $redis->lrange('request:1.3.3.7', 0, 1));

        $requestTracking->unregisterRequest();

        $this->assertCount(0, $redis->keys('request:1.3.3.7'));
    }

    public function testIdFromRedis()
    {
        $this->markTestIncomplete('the mock is faulty - we will make this into an integration test at a later point in time.');

        $redis = $this->getRedisMock();

        $key = 'request:42.42.42.42';
        $redis->lpush($key, ['huahuahuahua']);

        $requestTracking = new RequestTracking($redis);
        $this->assertEquals('huahuahuahua-1', $requestTracking->getRequestId());

        $this->assertCount(1, $redis->keys('request:1.3.3.7'));
        $this->assertEquals(['huahuahuahua-1'], $redis->lrange('request:1.3.3.7', 0, 1));

        $requestTracking->unregisterRequest();

        $this->assertCount(0, $redis->keys('request:1.3.3.7'));
    }

    /**
     * @return ClientInterface
     */
    private function getRedisMock(): ClientInterface
    {
        $factory   = new RedisMockFactory();

        /** @var ClientInterface $redisMock */
        $redisMock = $factory->getAdapter(Client::class, true);

        return $redisMock;
    }
}
