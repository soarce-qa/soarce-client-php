<?php

use Soarce\Config;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Config.php';

$config = new Config();
$config->setDataPath($argv[1]);

while (true) {
    if (file_exists($config->getDataPath() . DIRECTORY_SEPARATOR . Config::KILL_WORKER_FILENAME)) {
        die();
    }

    sleep(1);
}
