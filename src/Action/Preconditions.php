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
            'xdebug_installed'   => extension_loaded('xdebug'),
            'xdebug_3'           => str_starts_with(phpversion('xdebug'), '3'),
            'trace_format'       => ini_get('xdebug.trace_format') === '1',
            'trace_output_name'  => str_contains(ini_get('xdebug.trace_output_name'), '%u'),
            'outputdir_readable' => is_readable(ini_get('xdebug.output_dir')),
            'datadir_writable'   => is_writable($this->config->getDataPath()),
            'xdebug_mode'           => str_contains($mode, 'coverage') && str_contains($mode, 'profile') && str_contains($mode, 'trace'),
        ];

        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
