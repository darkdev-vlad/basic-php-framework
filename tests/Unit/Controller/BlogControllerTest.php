<?php

declare(strict_types=1);

namespace Test\Unit\Controller;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Xvladx\Controller\BlogController;

class BlogControllerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * Again, it's not a good idea to mock vendor's code
     * @test
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function entryActionSuccess(): void
    {
        $twig = $this->prophesize(Environment::class);
        $twig->render(Argument::any(), Argument::any())->shouldBeCalledOnce()->willReturn('test string');
        $blogController = new BlogController();

        $response = $blogController->entryAction(1, 2, 3, $twig->reveal());
        expect($response->getContent())->toBe('test string');
    }
}
