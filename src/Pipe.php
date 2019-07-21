<?php

namespace Soarce;

class Pipe
{
    /** @var string */
    private $basePath;

    /** @var callable */
    private $releaseFunction;
    /**
     * Pipe constructor.
     *
     * @param string   $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return string
     */
    public function getBasepath(): string
    {
        return $this->basePath;
    }

    /**
     * @return string
     */
    public function getFilenameLock(): string
    {
        return $this->basePath . Config::SUFFFIX_LOCK;
    }

    /**
     * @return string
     */
    public function getFilenameTracefile(): string
    {
        return $this->basePath . Config::SUFFIX_TRACEFILE;
    }

    /**
     * @param callable $function
     */
    public function registerReleaseFunction(callable $function): void
    {
        $this->releaseFunction = $function;
    }

    /**
     * release redis lock
     */
    public function __destruct()
    {
        if (null !== $this->releaseFunction) {
            call_user_func($this->releaseFunction);
        }
    }
}