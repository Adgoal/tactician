<?php

namespace League\Tactician\Tests\Handler\Locator;

use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Tests\Fixtures\Command\AddTaskCommand;
use League\Tactician\Tests\Fixtures\Command\CompleteTaskCommand;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class InMemoryLocatorTest
 *
 * @package League\Tactician\Tests\Handler\Locator
 */
class InMemoryLocatorTest extends TestCase
{
    /**
     * @var InMemoryLocator
     */
    private $inMemoryLocator;

    protected function setUp(): void
    {
        $this->inMemoryLocator = new InMemoryLocator();
    }

    public function testHandlerIsReturnedForSpecificClass(): void
    {
        $handler = new stdClass();

        $this->inMemoryLocator->addHandler($handler, CompleteTaskCommand::class);

        self::assertSame(
            $handler,
            $this->inMemoryLocator->getHandlerForCommand(CompleteTaskCommand::class)
        );
    }

    public function testConstructorAcceptsMapOfCommandClassesToHandlers(): void
    {
        $commandToHandlerMap = [
            AddTaskCommand::class => new stdClass(),
            CompleteTaskCommand::class => new stdClass()
        ];

        $locator = new InMemoryLocator($commandToHandlerMap);

        self::assertSame(
            $commandToHandlerMap[AddTaskCommand::class],
            $locator->getHandlerForCommand(AddTaskCommand::class)
        );

        self::assertSame(
            $commandToHandlerMap[CompleteTaskCommand::class],
            $locator->getHandlerForCommand(CompleteTaskCommand::class)
        );
    }

    public function testHandlerMissing(): void
    {
        $this->expectException(MissingHandlerException::class);
        $this->inMemoryLocator->getHandlerForCommand(CompleteTaskCommand::class);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
