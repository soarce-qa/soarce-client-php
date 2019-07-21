<?php

define('SOARCE_SKIP_EXECUTE', true);

use Soarce\Config;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../../../autoload.php';
}

$config = new Config();
$config->setDataPath($argv[1]);

while (true) {
    usleep(random_int(90000, 110000));

    $file = $config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, $argv[2]) . '.' . Config::SUFFIX_TRACEFILE;
    $fp = fopen($file, 'rb');
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

    if ('trace' === $temp['type']) {
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
    } elseif('coverage' === $temp['type']) {
        $data['payload'] = json_decode(stream_get_contents($fp), JSON_OBJECT_AS_ARRAY);
    }

    // send to service
    $opts = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json",
            'content' => json_encode($data, JSON_PRETTY_PRINT),
        ],
    ];

    $context = stream_context_create($opts);

    file_get_contents('http://soarce.local/receive', false, $context);
}
