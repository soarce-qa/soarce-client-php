<?php

namespace Soarce;

use Predis\ClientInterface;

class HashManager
{
    private const PREFIX = 'filehashes:';
    private const TIMEOUT = 3600;

    /** @var ClientInterface */
    private $client;

    /** @var string */
    private $applicationName;

    /** @var string[] */
    private $store = [];

    /** @var string[] */
    private $new = [];

    /**
     * HashManager constructor.
     *
     * @param ClientInterface $predis
     * @param string $applicationName
     */
    public function __construct(ClientInterface $predis, string $applicationName)
    {
        $this->applicationName = $applicationName;
        $this->client = $predis;
    }

    public function load(): void
    {
        $this->store = [];
        if (is_array($res = $this->client->hgetall(self::PREFIX . $this->applicationName))) {
            $this->store = $res;
        }
    }

    public function getMd5ForFile(string $filepath): string
    {
        if (strpos($filepath, "eval()'d code") !== false) {
            return '';
        }

        if (!isset($this->store[$filepath])) {
            $md5 = md5_file($filepath);
            $this->new[$filepath] = $md5;
            $this->store[$filepath] = $md5;
        }

        return $this->store[$filepath];
    }

    /**
     * @param  string[] $files
     * @return string[]
     */
    public function getMd5ForFiles(array $files): array
    {
        $return = [];
        foreach ($files as $file){
            $return[$file] = $this->getMd5ForFile($file);
        }
        return $return;
    }

    public function save(): void
    {
       foreach ($this->new as $path => $md5) {
           $this->client->hset(self::PREFIX . $this->applicationName, $path, $md5);
       }
       $this->client->expire(self::PREFIX . $this->applicationName, self::TIMEOUT);
    }

    public function reset(): void
    {
        $this->client->del([self::PREFIX . $this->applicationName]);
    }
}
