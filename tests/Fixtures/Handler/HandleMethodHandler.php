<?php

namespace League\Tactician\Tests\Fixtures\Handler;

/**
 * Sample handler that has all commands specified as individual methods, rather
 * than using magic methods like __call or __invoke.
 */
class HandleMethodHandler
{
    /**
     * @param $command
     */
    public function handle($command): void
    {
    }
}
