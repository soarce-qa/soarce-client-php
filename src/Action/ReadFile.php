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

        if (!$this->isPathPermittedByWhitelist($_GET['filename'])) {
            throw new Exception('File not found, not readable and/or not in a whitelisted directory', Exception::FILE_NOT_FOUND);
        }

        if (php_sapi_name() !== 'cli') {
            header (self::CHECKSUM_HEADER . ': ' . md5_file($_GET['filename']));
        }

        return file_get_contents($_GET['filename']);
    }

    /**
     * @param  string $filename
     * @return boolean
     */
    private function isPathPermittedByWhitelist($filename): bool
    {
        $realPath = realpath($filename);

        if (false === $realPath) {
            return false;
        }

        // no whitelist means access to all!
        if ([] === $this->config->getWhitelistedPaths()) {
            return true;
        }

        foreach ($this->config->getWhitelistedPaths() as $whitelistedPath) {
            if (0 === strpos($realPath, $whitelistedPath)) {
                return true;
            }
        }

        return false;
    }
}
