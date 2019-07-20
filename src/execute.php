<?php

use Soarce\Config;
use Soarce\FrontController;

$SOARCEconfig = new Config();
$SOARCEoutput = (new FrontController($SOARCEconfig))->run();
if ('' !== $SOARCEoutput) {
    die($SOARCEoutput);
}

define('SOARCE_REQUEST_ID', bin2hex(random_bytes(16)));

if ($SOARCEconfig->isTracingActive()) {
    //@todo figure out the filename to use by evaluating the locks
    $SOARCEpath = $SOARCEconfig->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, 0);

    $SOARCEfp = fopen($SOARCEpath . '.' . Config::SUFFIX_TRACEFILE, 'wb');
    fwrite($SOARCEfp, json_encode([
        'type' => 'trace',
        'request_time' => microtime(true),
        'request_id' => SOARCE_REQUEST_ID,
        'get'  => $_GET,
        'post' => $_POST,
        'server' => $_SERVER,
        'env' => $_ENV,
    ]) . "\n");

    usleep(10000);

    /** @noinspection ForgottenDebugOutputInspection */
    /** @noinspection PhpComposerExtensionStubsInspection */
    xdebug_start_code_coverage();

    /** @noinspection ForgottenDebugOutputInspection */
    /** @noinspection PhpComposerExtensionStubsInspection */
    xdebug_start_trace(
        $SOARCEpath,
        XDEBUG_TRACE_COMPUTERIZED
    );

    register_shutdown_function(static function () use ($SOARCEpath) {
        /** @noinspection ForgottenDebugOutputInspection */
        /** @noinspection PhpComposerExtensionStubsInspection */
        $coverage = xdebug_get_code_coverage();

        /** @noinspection ForgottenDebugOutputInspection */
        /** @noinspection PhpComposerExtensionStubsInspection */
        xdebug_stop_trace();

        usleep(10000);

        $outfile = $SOARCEpath . '.' . Config::SUFFIX_TRACEFILE;

        file_put_contents($outfile, json_encode([
                'type' => 'coverage',
                'request_time' => microtime(true),
                'request_id' => SOARCE_REQUEST_ID,
                'get'  => $_GET,
                'post' => $_POST,
                'server' => $_SERVER,
                'env' => $_ENV,
            ]) . "\n" . serialize($coverage)
        );
    });
}
