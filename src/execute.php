<?php

use Soarce\Config;
use Soarce\FrontController;

$output = (new FrontController(new Config()))->run();
if ('' !== $output) {
    die($output);
}
