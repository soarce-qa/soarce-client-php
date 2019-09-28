<?php

namespace Soarce;

use Predis\ClientInterface;

class RedisMutex
{
    /** @var string */
    private $name;

    /** @var ClientInterface */
    private $client;

    /** @var int */
    private $numberOfPipes;

    /**
     * RedisMutex constructor.
     *
     * @param ClientInterface $client
     * @param string          $name
     * @param int             $numberOfPipes
     */
    public function __construct(ClientInterface $client, $name, $numberOfPipes = null)
    {
        $this->name          = $name;
        $this->numberOfPipes = $numberOfPipes;
        $this->client        = $client;
    }

    /**
     */
    public function seed()
    {
        $this->clean();
        foreach ($this->allLockNames() as $num => $lock) {
            $this->client->lpush($lock, array($num));
        }
    }

    /**
     */
    public function clean()
    {
        $this->client->del($this->allLockNames());
        $this->client->del($this->allWorkNames());
    }

    /**
     * @return int
     */
    public function obtainLock()
    {
        $id = $this->client->brpop($this->allLockNames(), 300)[1];
        $this->client->lpush("work:{$this->name}:{$id}", array($id));
        return $id;
    }

    /**
     * @param  int    $id
     * @return void
     */
    public function releaseLock($id)
    {
        $this->client->rpoplpush("work:{$this->name}:{$id}", "lock:{$this->name}:{$id}");
    }

    /**
     * @return string[]
     */
    protected function allLockNames()
    {
        return $this->allNames('lock');
    }

    /**
     * @return string[]
     */
    protected function allWorkNames()
    {
        return $this->allNames('work');
    }

    /**
     * @param  string   $prefix
     * @return string[]
     */
    protected function allNames($prefix)
    {
        if (null === $this->numberOfPipes) {
            throw new Exception('unknown number of pipes, cannot run command');
        }

        $list = array();
        for ($i = 0; $i < $this->numberOfPipes; $i++) {
            $list[$i] = "{$prefix}:{$this->name}:$i";
        }
        return $list;
    }
}
