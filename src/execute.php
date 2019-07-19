<?php

use Soarce\Config;
use Soarce\FrontController;

$SOARCEconfig = new Config();
$SOARCEoutput = (new FrontController($SOARCEconfig))->run();
if ('' !== $SOARCEoutput) {
    die($SOARCEoutput);
}

if ($SOARCEconfig->isTracingActive()) {
    $SOARCEpath = $SOARCEconfig->getDataPath() . DIRECTORY_SEPARATOR . sprintf(Config::PIPE_NAME_TEMPLATE, 0);

    $SOARCEfp = fopen($SOARCEpath, 'wb');
    fwrite($SOARCEfp, json_encode([
        'type' => 'trace',
        'request_time' => microtime(true),
        'request_id' => 'filled-later',
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
    xdebug_start_trace(    //@todo figure out the filename to use by evaluating the locks
        $SOARCEpath,
        XDEBUG_TRACE_COMPUTERIZED
    );

    register_shutdown_function(static function () use ($SOARCEpath) {
        /** @noinspection ForgottenDebugOutputInspection */
        /** @noinspection PhpComposerExtensionStubsInspection */
        xdebug_stop_trace();

        usleep(10000);

        $outfile = $SOARCEpath . '.' . Config::SUFFIX_TRACEFILE;

        /** @noinspection ForgottenDebugOutputInspection */
        /** @noinspection PhpComposerExtensionStubsInspection */
        file_put_contents($outfile, json_encode([
                'type' => 'trace',
                'request_time' => microtime(true),
                'request_id' => 'filled-later',
                'get'  => $_GET,
                'post' => $_POST,
                'server' => $_SERVER,
                'env' => $_ENV,
            ]) . "\n" . json_encode(xdebug_get_code_coverage())
        );
    });
}
