<?php

namespace Soarce\Action;

use Soarce\Action;
use Soarce\Config;

class End extends Action
{
    /**
     * creates the usecase's directory, if it exists, it is cleared out
     *
     * @param  string $name
     * @return string
     * @throws Exception
     */
    private function createSubdir($name): string
    {
        $path = $this->config->getDataPath() . DIRECTORY_SEPARATOR . $this->filterUsecase($name);
        if (!is_dir($path)) {
            if (!mkdir($path) && !is_dir($path)) {
                throw new Exception(sprintf('Directory "%s" was not created', $path));
            }
        } else {
            $dirIter = new \DirectoryIterator($path);
            foreach ($dirIter as $file) {
                if ($file->isFile()) {
                    unlink($file->getRealPath());
                }
            }
        }
        return $path;
    }

    /**
     * @param  string $fullPathDestination
     * @throws Exception
     */
    private function moveFiles($fullPathDestination): void
    {
        // traces
        $traceFileDirectory = trim(ini_get('xdebug.trace_output_dir'));
        if ('' === $traceFileDirectory || '/' === $traceFileDirectory || !is_readable($traceFileDirectory)) {
            throw new Exception('Tracefile Directory does not exist or is not readable.', Exception::TRACEFILE_DIRECTORY_NOT_READABLE);
        }

        $dirIter = new \DirectoryIterator($traceFileDirectory);
        foreach ($dirIter as $file) {
            if ($file->isDir() || ($file->getExtension() !== Config::SUFFIX_TRACEFILE && $file->getExtension() !== Config::SUFFIX_COVERAGEFILE)) {
                continue;
            }

            rename(
                $file->getRealPath(),
                $fullPathDestination . DIRECTORY_SEPARATOR . $file->getFilename()
            );
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function run(): string
    {
        if (!is_writable($this->config->getDataPath())) {
            throw new Exception('data dir does not exist, is not writable or full', Exception::DATA_DIRECTORY__NOT_WRITEABLE);
        }

        if (!isset($_GET['usecase']) || '' === trim($_GET['usecase']) || '' === $this->filterUsecase($_GET['usecase'])) {
            throw new Exception('name of usecase is missing from request', Exception::NAME_OF_USECASE_MISSING_IN_REQUEST);
        }

        $fullPath = $this->createSubdir($_GET['usecase']);
        $this->moveFiles($fullPath);
        touch($fullPath . DIRECTORY_SEPARATOR . Config::COMPLETED_FILENAME);
        unlink($this->config->getDataPath() . DIRECTORY_SEPARATOR . Config::TRIGGER_FILENAME);

        return '';
    }
}
