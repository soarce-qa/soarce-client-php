<?php
/** @noinspection PhpComposerExtensionStubsInspection */
/** @noinspection ForgottenDebugOutputInspection */

namespace SoarceRuntime;

if (defined('SOARCE_SKIP_EXECUTE')) {
    return;
}

use Predis\Client;
use Soarce\Config;
use Soarce\FrontController;
use Soarce\HashManager;
use Soarce\Pipe\Handler;
use Soarce\RedisMutex;
use Soarce\RequestTracking;

$config = new Config();
$frontController = new FrontController($config);
$output = $frontController->run();
if ('' !== $output) {
    die($output);
}

if ($config->isTracingActive()) {

    $predisClient = new Client(array(
        'scheme' => 'tcp',
        'host'   => 'soarce.local',
        'port'   => 6379,
    ));

    $requestTracking = new RequestTracking($predisClient);

    $redisMutex = new RedisMutex($predisClient, $config->getApplicationName(), $config->getNumberOfPipes());
    $pipeHandler = new Handler($config, $redisMutex);
    $tracePipe = $pipeHandler->getFreePipe();
    $header = array(
        'type' => 'trace',
        'host' => $config->getApplicationName(),
        'request_time' => microtime(true),
        'request_id' => $requestTracking->getRequestId(),
        'get'  => $_GET,
        'post' => $_POST,
        'server' => $_SERVER,
        'env' => $_ENV,
    );

    $fpTracefile = fopen($tracePipe->getFilenameTracefile(), 'wb');
    fwrite($fpTracefile, json_encode($header) . "\n");

    xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);

    xdebug_start_trace(
        $tracePipe->getBasepath(),
        XDEBUG_TRACE_COMPUTERIZED
    );


    register_shutdown_function(static function () use ($header, $predisClient, $requestTracking){
        xdebug_stop_trace();

        // we'll do this as early as possible, to shorten the time where multiple requests could be registered in redis.
        $requestTracking->unregisterRequest();

        // preparing coverage payload
        $header['type'] = 'coverage';

        $data = array(
            'header' => $header,
            'payload' => xdebug_get_code_coverage(),
        );

        $hashManager = new HashManager($predisClient, $header['host']);
        $hashManager->load();
        $md5Hashes = $hashManager->getMd5ForFiles(array_keys($data['payload']));
        $data['md5'] = $md5Hashes;

        // send to service
        $opts = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => json_encode($data, JSON_PRETTY_PRINT),
            ),
        );

        $context = stream_context_create($opts);

        file_get_contents('http://soarce.local/receive', false, $context);

        $hashManager->save();
    });
}
