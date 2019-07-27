<?php

namespace Soarce\Action;

use Soarce\Action;

class Details extends Action
{
    /**
     * @return string
     */
    public function run(): string
    {
        $data = [
            'server'          => $_SERVER,
            'env'             => $_ENV,
            'extensions'      => get_loaded_extensions(),
            'zend_extensions' => get_loaded_extensions(true),
            'ini_settings'    => ini_get_all(),
        ];

        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
