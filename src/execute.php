<?php

use Soarce\FrontController;

$output = (new FrontController())->run();
if ('' !== $output) {
    die($output);
}
