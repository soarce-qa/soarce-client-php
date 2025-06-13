<?php

namespace Soarce\Action;

use Soarce\Action;

class Preconditions extends Action
{
    /**
     * @return string
     */
    public function run(): string
    {
        $mode = ini_get('xdebug.mode');
        $data = [
            'xdebug_installed'   => extension_loaded('xdebug'),
            'xdebug_3'           => strpos(phpversion('xdebug'), '3') === 0,
            'compression_off'    => ini_get('xdebug.use_compression') !== '1',
            'trace_format'       => ini_get('xdebug.trace_format') === '1',
            'trace_output_name'  => strpos(ini_get('xdebug.trace_output_name'), '%u') !== false,
            'outputdir_readable' => is_readable(ini_get('xdebug.output_dir')),
            'datadir_writable'   => is_writable($this->config->getDataPath()),
            'xdebug_mode'        => strpos($mode, 'coverage') !== false && strpos($mode, 'trace') !== false,
        ];

        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
