<?php

use Xvladx\Kernel\HttpKernel;

require __DIR__ . '/../vendor/autoload.php';

$kernel = new HttpKernel(__DIR__ . '/../config');
$kernel->handleRequest();
