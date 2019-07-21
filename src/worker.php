<?php

namespace SoarceRuntime;

define('SOARCE_SKIP_EXECUTE', true);

use Soarce\Config;
use Soarce\Pipe;
use Soarce\RedisMutex;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../../../autoload.php';
}

$config = new Config();
$config->setDataPath($argv[1]);

$id = $argv[2];
$redisMutex = RedisMutex::getInstance($_SERVER['HOSTNAME']);

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

    $data = [
        'header' => $temp,
        'payload' => [],
    ];

    $parsedData = [];
    while (false !== ($line = fgets($fp))) {
        $out = [];
        if (preg_match('/^[\d]+\t[\d]+\t[\d]+\t[\d\.]+[\d]+\t[\d]+\t([^\t]+)\t(0|1)\t[^\t]{0,}\t([^\t]+)\t.*/', $line, $out)) {
            if (!isset($parsedData[$out[3]])) {
                $parsedData[$out[3]] = [];
            }

            $parsedData[$out[3]][$out[1]] = $out[2];
        }
    }
    $data['payload'] = $parsedData;

    // send to service
    $opts = [
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/json',
            'content' => json_encode($data, JSON_PRETTY_PRINT),
        ],
    ];

    $context = stream_context_create($opts);

    file_get_contents('http://soarce.local/receive', false, $context);

    $redisMutex->releaseLock($id);
}

unlink ($pidfile);
