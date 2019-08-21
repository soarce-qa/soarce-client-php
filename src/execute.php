<?php
/** @noinspection PhpComposerExtensionStubsInspection */
/** @noinspection ForgottenDebugOutputInspection */

namespace SoarceRuntime;

if (defined('SOARCE_SKIP_EXECUTE')) {
    return;
}

use Soarce\Config;
use Soarce\FrontController;
use Soarce\HashManager;
use Soarce\Pipe\Handler;
use Soarce\RedisMutex;

$config = new Config();
$output = (new FrontController($config))->run();
if ('' !== $output) {
    die($output);
}

define('SOARCE_REQUEST_ID', bin2hex(random_bytes(16))); //TODO implement request-id-forwarding

if ($config->isTracingActive()) {
    $redisMutex = RedisMutex::getInstance($config->getApplicationName(), $config->getNumberOfPipes());
    $pipeHandler = new Handler($config, $redisMutex);
    $tracePipe = $pipeHandler->getFreePipe();
    $header = [
        'type' => 'trace',
        'host' => $config->getApplicationName(),
        'request_time' => microtime(true),
        'request_id' => SOARCE_REQUEST_ID,
        'get'  => $_GET,
        'post' => $_POST,
        'server' => $_SERVER,
        'env' => $_ENV,
    ];

    $fpTracefile = fopen($tracePipe->getFilenameTracefile(), 'wb');
    fwrite($fpTracefile, json_encode($header) . "\n");

    xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);

    xdebug_start_trace(
        $tracePipe->getBasepath(),
        XDEBUG_TRACE_COMPUTERIZED
    );

    register_shutdown_function(static function () use ($header){
        xdebug_stop_trace();

        $header['type'] = 'coverage';

        $data = [
            'header' => $header,
            'payload' => xdebug_get_code_coverage(),
        ];

        $hashManager = new HashManager($header['host']);
        $hashManager->load();
        $md5Hashes = $hashManager->getMd5ForFiles(array_keys($data['payload']));
        $data['md5'] = $md5Hashes;

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

        $hashManager->save();
    });
}
