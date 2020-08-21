<?php

namespace League\Tactician\Tests\Exception;

use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Tests\Fixtures\Command\CompleteTaskCommand;
use League\Tactician\Exception\Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class MissingHandlerExceptionTest
 *
 * @package League\Tactician\Tests\Exception
 */
class MissingHandlerExceptionTest extends TestCase
{
    public function testExceptionContainsDebuggingInfo(): void
    {
        $exception = MissingHandlerException::forCommand(CompleteTaskCommand::class);

        self::assertStringContainsString(CompleteTaskCommand::class, $exception->getMessage());
        self::assertSame(CompleteTaskCommand::class, $exception->getCommandName());
        self::assertInstanceOf(Exception::class, $exception);
    }
}
