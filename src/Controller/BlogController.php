<?php

declare(strict_types=1);

namespace Xvladx\Controller;

use Symfony\Component\HttpFoundation\Response;

class BlogController
{
    public function entryAction(int $year, int $month, int $day): Response
    {
        return new Response('Unknown reakajsjk');
    }
}
