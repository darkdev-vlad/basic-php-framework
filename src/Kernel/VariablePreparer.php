<?php

declare(strict_types=1);

namespace Xvladx\Kernel;

use ReflectionClass;
use ReflectionException;
use Webmozart\Assert\Assert;

class VariablePreparer
{
    /**
     * @param string $className
     * @param string $actionName
     * @param array<mixed> $parameters
     * @return array<mixed>
     * @throws ParameterException
     * @throws ReflectionException
     */
    public function prepareParameters(string $className, string $actionName, array $parameters): array
    {
        Assert::classExists($className);

        $reflectionClass = new ReflectionClass($className);
        $method = $reflectionClass->getMethod($actionName);

        //TODO here should be implemented some proper normalizer in normal cases because now it may fail casting value
        foreach ($method->getParameters() as $reflectionParameter) {
            $paramName = $reflectionParameter->getName();

            if (
                !isset($parameters[$paramName]) &&
                !$reflectionParameter->isOptional()
            ) {
                throw new ParameterException("Parameter {$reflectionParameter->getName()} has no value");
            }

            if (
                ($type = $reflectionParameter->getType()) !== null
            ) {
                settype($parameters[$paramName], $type->getName());
            }
        }

        return $parameters;
    }
}
