<?php

namespace SoarceRuntime;

define('SOARCE_SKIP_EXECUTE', true);

use Soarce\ParallelProcessDispatcher\Dispatcher;
use Soarce\ParallelProcessDispatcher\Process;
use Soarce\Config;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../../../autoload.php';
}

$config = new Config();
$config->setDataPath($argv[1]);
$config->setNumberOfPipes($argv[2]);

$dispatcher = new Dispatcher($config->getNumberOfPipes());

for ($i = 0; $i < $config->getNumberOfPipes(); $i++) {
    $process = new Process('php -f ' . __DIR__ . '/worker.php ' . $config->getDataPath() . ' ' . $i);
    $dispatcher->addProcess($process, true);
}

$pidfile = $config->getDataPath() . DIRECTORY_SEPARATOR . 'worker.pid';
file_put_contents($pidfile, getmypid());

while (true) {
    $dispatcher->tick();
    sleep(1);
}

unlink ($config->getDataPath() . DIRECTORY_SEPARATOR . 'worker.pid');
