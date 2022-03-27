<?php

declare(strict_types=1);

namespace Xvladx\Kernel;

use Exception;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as DependencyInjectionFileLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Loader\YamlFileLoader as RoutingYamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class HttpKernel
{
    private RequestContext $requestContext;
    private UrlMatcher $urlMatcher;
    private ContainerInterface $container;

    /**
     * @throws Exception
     */
    public function __construct(string $configPath)
    {
        $fileLocator = new FileLocator($configPath);

        $containerBuilder = new ContainerBuilder();
        $loader = new DependencyInjectionFileLoader($containerBuilder, $fileLocator);
        $loader->load('services.yaml');
        $containerBuilder->compile();

        $routingLoader = new RoutingYamlFileLoader($fileLocator);
        $routes = $routingLoader->load('routes.yaml');

        $this->requestContext = new RequestContext();
        $this->urlMatcher = new UrlMatcher($routes, $this->requestContext);
        $this->container = $containerBuilder;
    }

    public function handleRequest(): void
    {
        $request = Request::createFromGlobals();
        $this->requestContext->fromRequest($request);

        $parameters = $this->urlMatcher->match($this->requestContext->getPathInfo());

        [$controller, $action] = explode('::', $parameters['_controller']);
        unset($parameters['_controller'], $parameters['_route']);

        $vp = new VariablePreparer();
        $params = $vp->prepareParameters($controller, $action, $parameters);

        $controllerObject = $this->container->get($controller);

        /** @var Response $response */
        $response = $controllerObject->$action(...$params);

        $response->send();
    }
}
