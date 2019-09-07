<?php

namespace Soarce;

use Predis\Client;

class RedisMutex
{
    /** @var Client */
    private $client;

    /** @var int */
    private $numberOfPipes;

    /**
     * RedisMutex constructor.
     *
     * @param Client $client
     * @param string $name
     * @param int    $numberOfPipes
     */
    public function __construct(Client $client, $name, $numberOfPipes = null)
    {
        $this->name          = $name;
        $this->numberOfPipes = $numberOfPipes;
        $this->client        = $client;
    }

    /**
     */
    public function seed(): void
    {
        $this->clean();
        foreach ($this->allLockNames() as $num => $lock) {
            $this->client->lpush($lock, [$num]);
        }
    }

    /**
     */
    public function clean(): void
    {
        $this->client->del($this->allLockNames());
        $this->client->del($this->allWorkNames());
    }

    /**
     * @return int
     */
    public function obtainLock(): int
    {
        $id = $this->client->brpop($this->allLockNames(), 300)[1];
        $this->client->lpush("work:{$this->name}:{$id}", [$id]);
        return $id;
    }

    /**
     * @param  int    $id
     * @return void
     */
    public function releaseLock($id): void
    {
        $this->client->rpoplpush("work:{$this->name}:{$id}", "lock:{$this->name}:{$id}");
    }

    /**
     * @return string[]
     */
    protected function allLockNames(): array
    {
        return $this->allNames('lock');
    }

    /**
     * @return string[]
     */
    protected function allWorkNames(): array
    {
        return $this->allNames('work');
    }

    /**
     * @param  string   $prefix
     * @return string[]
     */
    protected function allNames($prefix): array
    {
        if (null === $this->numberOfPipes) {
            throw new Exception('unknown number of pipes, cannot run command');
        }

        $list = [];
        for ($i = 0; $i < $this->numberOfPipes; $i++) {
            $list[$i] = "{$prefix}:{$this->name}:$i";
        }
        return $list;
    }
}
