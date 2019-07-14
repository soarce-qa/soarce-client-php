<?php

namespace Soarce\Action;

use Soarce\Action;

class Clear extends Action
{
    /**
     * @return string
     */
    public function run(): string
    {
        if (!is_writable($this->config->getDataPath()) || !is_dir($this->config->getDataPath())) {
            throw new Exception('data dir does not exist or is not writable', Exception::DATA_DIRECTORY__NOT_WRITEABLE);
        }

        if (!isset($_GET['usecase']) || '' === trim($_GET['usecase']) || '' === $this->filterUsecase($_GET['usecase'])) {
            throw new Exception('name of usecase is missing from request', Exception::NAME_OF_USECASE_MISSING_IN_REQUEST);
        }

        $pathToDelete = $this->config->getDataPath() . DIRECTORY_SEPARATOR . $_GET['usecase'];

        if (!is_dir($pathToDelete)) {
            return json_encode(['deleted_files' => 0], JSON_PRETTY_PRINT);
        }

        $count = 0;
        $subDirIter = new \DirectoryIterator($pathToDelete);
        foreach ($subDirIter as $file) {
            if ($file->isDir()) {
                continue;
            }

            unlink($file->getPathname());
            $count++;
        }

        rmdir($pathToDelete);

        return json_encode(['deleted_files' => $count], JSON_PRETTY_PRINT);
    }
}
