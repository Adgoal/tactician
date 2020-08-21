<?php

namespace League\Tactician\Tests\Exception;

use League\Tactician\Exception\InvalidCommandException;
use League\Tactician\Exception\Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class InvalidCommandExceptionTest
 *
 * @package League\Tactician\Tests\Exception
 */
class InvalidCommandExceptionTest extends TestCase
{
    public function testExceptionContainsDebuggingInfo(): void
    {
        $command = 'must be an object';

        $exception = InvalidCommandException::forUnknownValue($command);

        self::assertStringContainsString('type: string', $exception->getMessage());
        self::assertSame($command, $exception->getInvalidCommand());
        self::assertInstanceOf(Exception::class, $exception);
    }
}
