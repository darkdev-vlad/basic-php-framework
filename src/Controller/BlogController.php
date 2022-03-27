<?php

declare(strict_types=1);

namespace Xvladx\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BlogController extends BaseController
{
    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function entryAction(int $year, int $month, int $day): Response
    {
        $test = $this->getTwig()->render('blog/entry.html.twig', [
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]);

        return new Response($test);
    }
}
