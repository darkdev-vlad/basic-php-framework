<?php

declare(strict_types=1);

namespace Test\Fake;

use Symfony\Component\HttpFoundation\Response;

class FakeController
{
    public function testAction(int $arg1, string $arg2, $arg3): Response
    {
        return new Response("{$arg1}, {$arg2}, {$arg3}");
    }

    public function test2Action(FakeService $fakeService): Response
    {
        return new Response($fakeService->testMethod());
    }
}
