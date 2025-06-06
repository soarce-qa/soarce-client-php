<?php

namespace Soarce;

class Pipe
{
    /** @var callable */
    private $releaseFunction;
    /**
     * Pipe constructor.
     */
    public function __construct(private string $basePath)
    {}

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