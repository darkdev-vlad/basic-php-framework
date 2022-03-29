<?php

declare(strict_types=1);

namespace Test\Unit\Kernel;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Xvladx\Kernel\HttpKernel;

class HttpKernelTest extends TestCase
{
    /**
     * @test
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function constructs(): void
    {
        $httpKernel = new HttpKernel(__DIR__ . '/../../../config');

        expect($httpKernel)->toBeInstanceOf(HttpKernel::class);
    }

    /**
     * @test
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function constructFails(): void
    {
        $nonExistingPath = __DIR__ . '/unknownPath/FileIsNotHere';

        $this->expectException(FileLocatorFileNotFoundException::class);
        $this->expectExceptionMessage('The file "services.yaml" does not exist ' .
            "(in: \"{$nonExistingPath}\").");

        new HttpKernel($nonExistingPath);
    }
}
