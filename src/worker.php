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
$redisMutex = RedisMutex::getInstance($config->getApplicationName());

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

    $parseStack = [];
    $parsedData = [];
    while (false !== ($line = fgets($fp))) {
        $out = [];
        if (preg_match('/^[\d]+\t(?P<functionNumber>[\d]+)\t0\t(?P<start>[\d.]+)\t[\d]+\t(?P<function>[^\t]+)\t(?P<type>[01])\t[^\t]*\t(?P<file>[^\t]+)\t.*/', $line, $out)) {
            $parseStack[$out['functionNumber']] = [
                'start'    => $out['start'],
                'function' => $out['function'],
                'type'     => $out['type'],
                'file'     => $out['file'],
            ];
            continue;
        }

        if (preg_match('/^[\d]+\t(?P<functionNumber>[\d]+)\t1\t(?P<end>[\d.]+)\t[\d]+.*/', $line, $out)) {
            $info = $parseStack[$out['functionNumber']];
            unset($parseStack[$out['functionNumber']]);

            if (!isset($parsedData[$info['file']])) {
                $parsedData[$info['file']] = [];
            }

            if (!isset($parsedData[$info['file']][$info['function']])) {
                $parsedData[$info['file']][$info['function']] = [
                    'type'     => $info['type'],
                    'count'    => 1,
                    'walltime' => $out['end'] - $info['start'],
                ];
            } else {
                $parsedData[$info['file']][$info['function']]['count']++;
                $parsedData[$info['file']][$info['function']]['walltime'] += ($out['end'] - $info['start']);
            }
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
