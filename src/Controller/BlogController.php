<?php

declare(strict_types=1);

namespace Xvladx\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class BlogController
{
    public function entryAction(int $year, int $month, int $day): Response
    {
        $loader = new FilesystemLoader('../templates');
        $twig = new Environment($loader);

        $test = $twig->render('blog/entry.html.twig', [
            'year' => $year
        ]);

        return new Response($test);
    }
}
