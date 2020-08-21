<?php

namespace League\Tactician\Tests\Setup;

use League\Tactician\CommandBus;
use League\Tactician\Setup\QuickStart;
use League\Tactician\Tests\Fixtures\Command\AddTaskCommand;
use League\Tactician\Tests\Fixtures\Command\CompleteTaskCommand;
use League\Tactician\Tests\Fixtures\Handler\HandleMethodHandler;
use PHPUnit\Framework\TestCase;

/**
 * Class QuickStartTest
 *
 * @package League\Tactician\Tests\Setup
 */
class QuickStartTest extends TestCase
{
    public function testReturnsACommandBus(): void
    {
        $commandBus = QuickStart::create([]);
        self::assertInstanceOf(CommandBus::class, $commandBus);
    }

    public function testCommandToHandlerMapIsProperlyConfigured(): void
    {
        $map = [
            AddTaskCommand::class => $this->createMock(HandleMethodHandler::class),
            CompleteTaskCommand::class => $this->createMock(HandleMethodHandler::class),
        ];

        $map[AddTaskCommand::class]->expects(self::once())->method('handle');
        $map[CompleteTaskCommand::class]->expects(self::never())->method('handle');

        $commandBus = QuickStart::create($map);
        $commandBus->handle(new AddTaskCommand());
    }
}
