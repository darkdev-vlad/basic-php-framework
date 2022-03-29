<?php

declare(strict_types=1);

namespace Xvladx\Kernel;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webmozart\Assert\Assert;

class ParametersResolver
{
    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * @param string $className
     * @param string $actionName
     * @param array<mixed> $parameters
     * @return array<mixed>
     * @throws ParameterException
     * @throws ReflectionException
     */
    public function resolve(string $className, string $actionName, array $parameters): array
    {
        Assert::classExists($className);

        $reflectionClass = new ReflectionClass($className);
        $method = $reflectionClass->getMethod($actionName);

        //TODO here should be implemented some proper normalizer in usual cases because now it may fail while converting value
        //Here's implemented very basic type casting
        foreach ($method->getParameters() as $reflectionParameter) {
            $paramName = $reflectionParameter->getName();
            $typeName = $reflectionParameter->getType()?->getName();
            $serviceExists = $this->container->has($typeName ?? '');

            if (
                !$serviceExists &&
                !isset($parameters[$paramName]) &&
                !$reflectionParameter->isOptional()
            ) {
                throw new ParameterException("Parameter {$reflectionParameter->getName()} has no value");
            }

            if (
                $typeName !== null &&
                $reflectionParameter->getType()?->isBuiltin()
            ) {
                settype($parameters[$paramName], $typeName);
            } elseif ($serviceExists && $typeName !== null) {
                $parameters[$paramName] = $this->container->get($typeName);
            }
        }

        return $parameters;
    }
}
