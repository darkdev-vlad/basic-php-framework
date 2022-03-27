<?php

declare(strict_types=1);

namespace Xvladx\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ErrorController extends BaseController
{
    /**
     * @param string $errorText
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showErrorAction(string $errorText): Response
    {
        return (new Response($this->getTwig()->render('error/error.html.twig', [
            'errorText' => $errorText
        ])))->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
