<?php

use Soarce\Config;
use Soarce\FrontController;

$SOARCEconfig = new Config();
$SOARCEoutput = (new FrontController($SOARCEconfig))->run();
if ('' !== $SOARCEoutput) {
    die($SOARCEoutput);
}

if (isset($_COOKIE['XDEBUG_TRACE']) && file_exists($SOARCEconfig->getDataPath() . DIRECTORY_SEPARATOR . Config::COMPLETED_FILENAME)) {
    /** @noinspection ForgottenDebugOutputInspection */
    /** @noinspection PhpComposerExtensionStubsInspection */
    xdebug_start_code_coverage();

    register_shutdown_function(static function () {
        /** @noinspection ForgottenDebugOutputInspection */
        /** @noinspection PhpComposerExtensionStubsInspection */
        $outfile = xdebug_get_tracefile_name() . '.coverage';

        /** @noinspection ForgottenDebugOutputInspection */
        /** @noinspection PhpComposerExtensionStubsInspection */
        file_put_contents($outfile, json_encode(xdebug_get_code_coverage()));
    });
}
