<?php

declare(strict_types=1);

namespace Xvladx\Kernel;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
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
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Xvladx\Controller\ErrorController;

class HttpKernel
{
    private RequestContext $requestContext;
    private UrlMatcher $urlMatcher;
    private ContainerInterface $container;
    private ParametersResolver $parametersResolver;

    /**
     * @param string $configPath
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
        $this->parametersResolver = $this->container->get(ParametersResolver::class);
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws LoaderError
     * @throws NotFoundExceptionInterface
     * @throws ParameterException
     * @throws ReflectionException
     * @throws RuntimeError
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

            $params = $this->parametersResolver->resolve($controller, $action, $parameters);
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
     * @param string $errorText
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws LoaderError
     * @throws NotFoundExceptionInterface
     * @throws ParameterException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws ReflectionException
     */
    private function getErrorResponse(string $errorText): Response
    {
        $parameters = $this->parametersResolver->resolve(
            ErrorController::class,
            'showErrorAction',
            ['errorText' => $errorText]
        );

        /** @var ErrorController $errorController */
        $errorController = $this->container->get(ErrorController::class);

        return $errorController->showErrorAction(...$parameters);
    }
}
