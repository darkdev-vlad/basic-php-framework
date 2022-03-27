<?php

require __DIR__ . '/../vendor/autoload.php';

$fileLocator = new \Symfony\Component\Config\FileLocator(__DIR__ . '/../config');
$loader = new \Symfony\Component\Routing\Loader\YamlFileLoader($fileLocator);

$routes = $loader->load('routes.yaml');

$context = new \Symfony\Component\Routing\RequestContext();
$context->fromRequest(\Symfony\Component\HttpFoundation\Request::createFromGlobals());

$matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

$parameters = $matcher->match($context->getPathInfo());

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

[$controller, $action] = explode('::', $parameters['_controller']);

$controllerObject = new $controller();

/** @var \Symfony\Component\HttpFoundation\Response $response */
$response = $controllerObject->$action($request);

$response->send();
