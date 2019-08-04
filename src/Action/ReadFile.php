<?php

namespace Soarce\Action;

use Soarce\Action;

class ReadFile extends Action
{
    private const CHECKSUM_HEADER = 'X-SOARCE-FileChecksum';

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

        if (php_sapi_name() !== 'cli') {
            header (self::CHECKSUM_HEADER . ': ' . md5_file($_GET['filename']));
        }
        return file_get_contents($_GET['filename']);
    }
}
