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
        $data = [
            'xdebug_installed'      => extension_loaded('xdebug'),
            'autotrace_off'         => ini_get('xdebug.auto_trace') === '0',
            'trace_format'          => ini_get('xdebug.trace_format') === '1',
            'trace_output_name'     => strpos(ini_get('xdebug.trace_output_name'), '%u') !== false,
            'trace_trigger_enabled' => ini_get('xdebug.trace_enable_trigger') === '1',
            'tracedir_readable'     => is_readable(ini_get('xdebug.trace_output_dir')),
            'datadir_writable'      => is_writable($this->config->getDataPath()),
            'debug'                 => [
                'server' => $_SERVER,
                'env'    => $_ENV,
            ],
        ];

        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
