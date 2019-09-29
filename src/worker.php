<?php

namespace SoarceRuntime;

define('SOARCE_SKIP_EXECUTE', true);

use Predis\Client;
use Soarce\Config;
use Soarce\Pipe;
use Soarce\RedisMutex;
use Soarce\TraceParser;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../../../autoload.php';
}

$config = new Config();
$config->setDataPath($argv[1]);

$id = $argv[2];

$predisClient = new Client(array(
    'scheme' => 'tcp',
    'host'   => 'soarce.local',
    'port'   => 6379,
));

$redisMutex = new RedisMutex($predisClient, $config->getApplicationName());

$pipe = new Pipe($config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, $id));

$pidfile = $config->getDataPath() . DIRECTORY_SEPARATOR . 'worker-' . $id . '.pid';
file_put_contents($pidfile, getmypid());

while (true) {
    $fp = fopen($pipe->getFilenameTracefile(), 'rb');
    $first = fgets($fp);
    if (false === $first) {
        continue;
    }

    $temp = json_decode($first, JSON_OBJECT_AS_ARRAY);

    if (null === $temp) {
        continue;
    }

    $traceParser = new TraceParser();
    $traceParser->analyze($fp);

    $data = array(
        'header' => $temp,
        'payload' => array(
            'functions' => $traceParser->getParsedData(),
            'calls'     => $traceParser->getFunctionMap(),
        ),
    );

    // send to service
    $opts = array(
        'http' => array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/json',
            'content' => json_encode($data),
        ),
    );

    $context = stream_context_create($opts);

    file_get_contents('http://soarce.local/receive', false, $context);

    $redisMutex->releaseLock($id);
}

unlink ($pidfile);
