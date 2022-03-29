<?php

declare(strict_types=1);

namespace Test\Unit\Kernel;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use ReflectionException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Test\Fake\FakeController;
use Test\Fake\FakeService;
use Xvladx\Kernel\ParameterException;
use Xvladx\Kernel\ParametersResolver;

class ParameterResolverTest extends TestCase
{
    use ProphecyTrait;

    private ParametersResolver $parameterResolver;
    private ContainerInterface|ObjectProphecy $container;

    protected function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->parameterResolver = new ParametersResolver($this->container->reveal());
    }

    /**
     * @test
     * @dataProvider argumentsProvider
     * @param array $arguments
     * @param array $expectedParams
     * @return void
     * @throws ParameterException
     * @throws ReflectionException
     */
    public function resolvesWithoutService(array $arguments, array $expectedParams): void
    {
        $params = $this->parameterResolver->resolve(FakeController::class, 'testAction', $arguments);

        expect($params)->toBe($expectedParams);
    }

    /**
     * It's not a good idea to mock vendor's code and better this need to be done as a functional test
     * @test
     * @return void
     * @throws ReflectionException
     * @throws ParameterException
     */
    public function resolvesWithService(): void
    {
        $fakeService = new FakeService();

        $this->container->has(FakeService::class)->shouldBeCalledOnce()->willReturn(true);
        $this->container->get(FakeService::class)->shouldBeCalledOnce()->willReturn(
            $fakeService
        );

        $params = $this->parameterResolver->resolve(FakeController::class, 'test2Action', []);

        expect($params)->toBe(['fakeService' => $fakeService]);
    }

    /**
     * @test
     * @dataProvider invalidArgumentsProvider
     * @param array $arguments
     * @param string $exceptionMessage
     * @return void
     * @throws ParameterException
     * @throws ReflectionException
     */
    public function conversionFails(array $arguments, string $exceptionMessage): void
    {
        $this->expectError();
        $this->expectExceptionMessage($exceptionMessage);

        $this->parameterResolver->resolve(FakeController::class, 'testAction', $arguments);
    }

    /**
     * @test
     * @dataProvider missingArgumentsProvider
     * @param array $arguments
     * @param string $exceptionMessage
     * @return void
     * @throws ParameterException
     * @throws ReflectionException
     */
    public function resolveWithoutArgsFails(array $arguments, string $exceptionMessage): void
    {
        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->parameterResolver->resolve(FakeController::class, 'testAction', $arguments);
    }

    public function argumentsProvider(): array
    {
        $stdClass = new \StdClass();

        return [
            [
                [
                    'arg1' => '1111',
                    'arg2' => '2222',
                    'arg3' => '3333',
                ],
                [
                    'arg1' => 1111,
                    'arg2' => '2222',
                    'arg3' => '3333',
                ],
            ],
            [
                [
                    'arg1' => 'sdkdks',
                    'arg2' => 1212,
                    'arg3' => $stdClass,
                ],
                [
                    'arg1' => 0,
                    'arg2' => '1212',
                    'arg3' => $stdClass,
                ],
            ],
        ];
    }

    public function invalidArgumentsProvider(): array
    {
        return [
            [
                [
                    'arg1' => new \StdClass(),
                    'arg2' => '2222',
                    'arg3' => '3333',
                ],
                'Object of class stdClass could not be converted to int'
            ],
            [
                [
                    'arg1' => '111',
                    'arg2' => new \StdClass(),
                    'arg3' => '3333',
                ],
                'Object of class stdClass could not be converted to string'
            ],
        ];
    }

    public function missingArgumentsProvider(): array
    {
        return [
            [
                [],
                'Parameter arg1 has no value',
            ],
            [
                ['arg1' => 'sdkdks',],
                'Parameter arg2 has no value',
            ],
            [
                ['arg1' => '2323', 'arg2' => '2323'],
                'Parameter arg3 has no value',
            ],
            [
                ['arg1' => null],
                'Parameter arg1 has no value'
            ],
        ];
    }
}
