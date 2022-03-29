<?php

declare(strict_types=1);

namespace Xvladx\Controller;

use Symfony\Component\HttpFoundation\Response;

class MainController
{
    public function indexAction(): Response
    {
        return new Response('Just an index page without any template engine as an example');
    }
}
