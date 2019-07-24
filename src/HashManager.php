<?php

namespace Soarce;

use Predis\Client;

class HashManager
{
    private const PREFIX = 'filehashes:';
    private const TIMEOUT = 3600;

    /** @var Client */
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
     * @param string $applicationName
     */
    public function __construct($applicationName)
    {
        $this->applicationName = $applicationName;
        $this->client = new Client([
            'scheme' => 'tcp',
            'host'   => 'soarce.local',
            'port'   => 6379,
        ]);
    }

    /**
     *
     */
    public function load(): void
    {
        $this->store = [];
        if (is_array($res = $this->client->hgetall(self::PREFIX . $this->applicationName))) {
            $this->store = $res;
        }
    }

    /**
     * @param  string $filepath
     * @return string
     */
    public function getMd5ForFile($filepath): string
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
    public function getMd5ForFiles($files): array
    {
        $return = [];
        foreach ($files as $file){
            $return[$file] = $this->getMd5ForFile($file);
        }
        return $return;
    }

    /**
     *
     */
    public function save(): void
    {
       foreach ($this->new as $path => $md5) {
           $this->client->hset(self::PREFIX . $this->applicationName, $path, $md5);
       }
       $this->client->expire(self::PREFIX . $this->applicationName, self::TIMEOUT);
    }

}
