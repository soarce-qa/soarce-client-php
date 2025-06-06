<?php

namespace Soarce;

use Predis\ClientInterface;

class RedisMutex
{
    /**
     * RedisMutex constructor.
     *
     * @param ClientInterface $client
     * @param string          $name
     * @param int|null $numberOfPipes
     */
    public function __construct(private ClientInterface $client, private string $name, private ?int $numberOfPipes = null)
    {}

    public function seed(): void
    {
        $this->clean();
        foreach ($this->allLockNames() as $num => $lock) {
            $this->client->lpush($lock, [$num]);
        }
    }

    public function clean(): void
    {
        $this->client->del($this->allLockNames());
        $this->client->del($this->allWorkNames());
    }

    public function obtainLock(): int
    {
        $id = $this->client->brpop($this->allLockNames(), 300)[1];
        $this->client->lpush("work:{$this->name}:{$id}", [$id]);
        return $id;
    }

    public function releaseLock(int $id): void
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
     * @param string $prefix
     * @return string[]
     */
    protected function allNames(string $prefix): array
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
