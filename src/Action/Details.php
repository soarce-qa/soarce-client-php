<?php

namespace Soarce\Action;

use Soarce\Action;

class Details extends Action
{
    /**
     * @return string
     */
    public function run()
    {
        $data = [
            'server'          => $_SERVER,
            'env'             => $_ENV,
            'extensions'      => get_loaded_extensions(),
            'ini_settings'    => ini_get_all(null, false),
        ];

        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
