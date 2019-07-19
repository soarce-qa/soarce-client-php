<?php

use Soarce\Config;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Config.php';

$config = new Config();
$config->setDataPath($argv[1]);
$config->setNumberOfPipes($argv[2]);

while (true) {
    if (file_exists($config->getDataPath() . DIRECTORY_SEPARATOR . Config::KILL_WORKER_FILENAME)) {
        die();
    }

    for ($i = 0; $i < $config->getNumberOfPipes(); $i++) {
        $file = $config->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, $i);
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
        $encodedPayload = http_build_query(['data' => $data]);

        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded",
                'content' => $encodedPayload,
            ],
        ];

        $context = stream_context_create($opts);

        $json = file_get_contents('http://soarce.local:8000/', false, $context);
    }

    usleep(100000);
}
