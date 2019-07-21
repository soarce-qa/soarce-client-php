<?php

namespace Soarce;

class Pipe
{
    /** @var string */
    private $basePath;

    /**
     * Pipe constructor.
     *
     * @param string $basePath
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
     * delete lock file on script end
     */
    public function __destruct()
    {
        if (file_exists($this->getFilenameLock())) {
            unlink ($this->getFilenameLock());
        }
    }
}