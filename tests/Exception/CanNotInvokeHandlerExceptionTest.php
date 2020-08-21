<?php

namespace League\Tactician\Tests\Exception;

use League\Tactician\Exception\CanNotInvokeHandlerException;
use League\Tactician\Exception\Exception;
use League\Tactician\Tests\Fixtures\Command\CompleteTaskCommand;
use Mockery;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use stdClass;

/**
 * Class CanNotInvokeHandlerExceptionTest
 *
 * @package League\Tactician\Tests\Exception
 */
class CanNotInvokeHandlerExceptionTest extends TestCase
{
    public function testExceptionContainsDebuggingInfo(): void
    {
        $command = new CompleteTaskCommand();

        $exception = CanNotInvokeHandlerException::forCommand($command, 'Because stuff');

        self::assertStringContainsString(CompleteTaskCommand::class, $exception->getMessage());
        self::assertStringContainsString('Because stuff', $exception->getMessage());
        self::assertSame($command, $exception->getCommand());
        self::assertInstanceOf(Exception::class, $exception);
    }

    /**
     * @dataProvider provideAnyTypeOfCommand
     *
     * @param $command
     */
    public function testForAnyTypeOfCommand($command): void
    {
        $exception = CanNotInvokeHandlerException::forCommand($command, 'happens');
        self::assertSame($command, $exception->getCommand());
    }

    public function provideAnyTypeOfCommand(): array
    {
        return [
            [ 1 ],
            [ new stdClass() ],
            [ null ],
            [ 'a string' ],
            [ new SplFileInfo(__FILE__) ],
            [ true ],
            [ false ],
            [ [] ],
            [ [ [ 1 ] ] ],
            [
                static function () {
                }
            ],
        ];
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
