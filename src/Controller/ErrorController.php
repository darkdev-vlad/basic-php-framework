<?php

declare(strict_types=1);

namespace Xvladx\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ErrorController
{
    /**
     * @param string $errorText
     * @param Environment $twig
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showErrorAction(string $errorText, Environment $twig): Response
    {
        return (new Response($twig->render('error/error.html.twig', [
            'errorText' => $errorText
        ])))->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
