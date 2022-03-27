<?php

declare(strict_types=1);

namespace Xvladx\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class BlogController
{
    private Environment $twig;

    /**
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function entryAction(int $year, int $month, int $day): Response
    {
        $test = $this->twig->render('blog/entry.html.twig', [
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]);

        return new Response($test);
    }
}
