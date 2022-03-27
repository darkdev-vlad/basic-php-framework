<?php

declare(strict_types=1);

namespace Xvladx\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController
{
    public function testAction(Request $request)
    {
        $n = 1;

        return new Response('This is my response');
    }
}
