<?php

declare(strict_types=1);

namespace Xvladx\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BlogController
{
    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @param Environment $twig
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function entryAction(int $year, int $month, int $day, Environment $twig): Response
    {
        return new Response($twig->render('blog/entry.html.twig', [
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]));
    }
}
