<?php

namespace Soarce\Action;

use Soarce\Action;

class ReadFile extends Action
{
    /**
     * @return string
     */
    public function run(): string
    {
        if (!isset($_GET['filename'])) {
            throw new Exception('Filename parameter not submitted', Exception::MISSING_FILENAME_PARAMETER);
        }
        if (!is_readable($_GET['filename'])) {
            throw new Exception('File not found or not readable', Exception::FILE_NOT_FOUND);
        }

        return file_get_contents($_GET['filename']);
    }
}
