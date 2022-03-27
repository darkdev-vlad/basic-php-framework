<?php

declare(strict_types=1);

namespace Xvladx\Controller;

use Twig\Environment;

abstract class BaseController
{
    public function __construct(private Environment $twig)
    {
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }
}
