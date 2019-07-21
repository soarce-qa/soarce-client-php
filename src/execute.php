<?php
/** @noinspection PhpComposerExtensionStubsInspection */
/** @noinspection ForgottenDebugOutputInspection */

namespace SoarceRuntime;

if (defined('SOARCE_SKIP_EXECUTE')) {
    return;
}

use Soarce\Config;
use Soarce\FrontController;
use Soarce\Pipe\Handler;

$config = new Config();
$output = (new FrontController($config))->run();
if ('' !== $output) {
    die($output);
}

define('SOARCE_REQUEST_ID', bin2hex(random_bytes(16))); //TODO implement request-id-forwarding

if ($config->isTracingActive()) {
    $pipeHandler = new Handler($config);
    $tracePipe = $pipeHandler->getFreePipe();
    $requestTime = microtime(true);

    $fpTracefile = fopen($tracePipe->getFilenameTracefile(), 'wb');
    fwrite($fpTracefile, json_encode([
            'type' => 'trace',
            'request_time' => $requestTime,
            'request_id' => SOARCE_REQUEST_ID,
            'get'  => $_GET,
            'post' => $_POST,
            'server' => $_SERVER,
            'env' => $_ENV,
        ]) . "\n");

    xdebug_start_code_coverage();

    xdebug_start_trace(
        $tracePipe->getBasepath(),
        XDEBUG_TRACE_COMPUTERIZED
    );

    register_shutdown_function(static function () use ($requestTime){
        xdebug_stop_trace();

        $data = [
            'header' => [
                'type' => 'coverage',
                'request_time' => $requestTime,
                'request_id' => SOARCE_REQUEST_ID,
                'get'  => $_GET,
                'post' => $_POST,
                'server' => $_SERVER,
                'env' => $_ENV,
            ],
            'payload' => xdebug_get_code_coverage(),
        ];

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
    });
}
