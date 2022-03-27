<?php

declare(strict_types=1);

use Xvladx\Kernel\HttpKernel;

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new HttpKernel(__DIR__ . '/../config');
