<?php

declare(strict_types=1);

namespace Xvladx\Kernel;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as DependencyInjectionFileLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Loader\YamlFileLoader as RoutingYamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Xvladx\Controller\ErrorController;

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

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function handleRequest(): void
    {
        try {
            $request = Request::createFromGlobals();
            $this->requestContext->fromRequest($request);

            $parameters = $this->urlMatcher->match($this->requestContext->getPathInfo());

            [$controller, $action] = explode('::', $parameters['_controller']);
            unset($parameters['_controller'], $parameters['_route']);

            $parametersResolver = $this->container->get(ParametersResolver::class);
            $params = $parametersResolver->resolve($controller, $action, $parameters);

            $controllerObject = $this->container->get($controller);

            /** @var Response $response */
            $response = $controllerObject->$action(...$params);
        } catch (ResourceNotFoundException) {
            $response = $this->getErrorResponse('404 not found');
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            $response = $this->getErrorResponse($e->getMessage());
        }

        $response->send();
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws SyntaxError
     * @throws ContainerExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    private function getErrorResponse(string $errorText): Response
    {
        /** @var ErrorController $errorController */
        $errorController = $this->container->get(ErrorController::class);

        return $errorController->showErrorAction($errorText, $this->container->get(Environment::class));
    }
}
