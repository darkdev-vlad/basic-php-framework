<?php

declare(strict_types=1);

namespace Xvladx\Controller;

use Exception;
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

    /**
     * @param Environment $twig
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function listAction(Environment $twig): Response
    {
        $randomRecords = [];

        for ($i = 0; $i < 10; $i++) {
            $randomRecords[] = random_int(1995, 2022);
        }

        return new Response($twig->render('blog/list.html.twig', [
            'records' => $randomRecords
        ]));
    }
}
