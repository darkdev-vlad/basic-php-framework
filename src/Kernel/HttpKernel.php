<?php

declare(strict_types=1);

namespace Xvladx\Kernel;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class HttpKernel
{
    private RequestContext $requestContext;
    private UrlMatcher $urlMatcher;

    public function __construct(string $configPath)
    {
        $fileLocator = new FileLocator($configPath);
        $loader = new YamlFileLoader($fileLocator);

        $routes = $loader->load('routes.yaml');

        $this->requestContext = new RequestContext();
        $this->urlMatcher = new UrlMatcher($routes, $this->requestContext);
    }

    public function handleRequest(): void
    {
        $request = Request::createFromGlobals();
        $this->requestContext->fromRequest($request);

        $parameters = $this->urlMatcher->match($this->requestContext->getPathInfo());

        [$controller, $action] = explode('::', $parameters['_controller']);

        $controllerObject = new $controller();

        /** @var Response $response */
        $response = $controllerObject->$action($request);

        $response->send();
    }
}
