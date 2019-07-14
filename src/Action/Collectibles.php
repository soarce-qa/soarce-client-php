<?php

namespace Soarce\Action;

use Soarce\Action;
use Soarce\Config;

class Collectibles extends Action
{
    /**
     * @return string
     */
    public function run(): string
    {
        if (!is_readable($this->config->getDataPath()) || !is_dir($this->config->getDataPath())) {
            throw new Exception('data dir does not exist, is not writable or full', Exception::DATA_DIRECTORY__NOT_READABLE);
        }

        $data = [];
        $mainDirIter = new \DirectoryIterator($this->config->getDataPath());
        foreach ($mainDirIter as $usecaseDirectory) {
            if (!$usecaseDirectory->isDir() || $usecaseDirectory->isDot()) {
                continue;
            }

            $data[$usecaseDirectory->getFilename()] = [];

            $subDirIter = new \DirectoryIterator($usecaseDirectory->getPathname());
            $completedFileFound = false;
            foreach ($subDirIter as $file) {
                if ($file->getFilename() === Config::COMPLETED_FILENAME) {
                    $completedFileFound = true;
                }
                if (!in_array($file->getExtension(), [Config::SUFFIX_TRACEFILE, Config::SUFFIX_COVERAGEFILE], true)) {
                    continue;
                }
                $data[$usecaseDirectory->getFilename()][] = $file->getFilename();
            }

            if ($completedFileFound === false || [] === $data[$usecaseDirectory->getFilename()]) {
                unset($data[$usecaseDirectory->getFilename()]);
            }
        }

        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
