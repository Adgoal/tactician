<?php

namespace League\Tactician\Tests\Plugins;

use Exception;
use League\Tactician\Plugins\LockingMiddleware;
use League\Tactician\Tests\Fixtures\Command\AddTaskCommand;
use League\Tactician\Tests\Fixtures\Command\CompleteTaskCommand;
use LogicException;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class LockingMiddlewareTest
 *
 * @package League\Tactician\Tests\Plugins
 */
class LockingMiddlewareTest extends TestCase
{
    /**
     * @var LockingMiddleware
     */
    private $lockingMiddleware;

    public function setUp(): void
    {
        $this->lockingMiddleware = new LockingMiddleware();
    }

    public function testInnerCommandBusReceivesCommand(): void
    {
        $command = new AddTaskCommand();

        $nextClosure = function ($command) {
            $this->assertIsObject($command);
            return 'foobar';
        };

        self::assertEquals(
            'foobar',
            $this->lockingMiddleware->execute($command, $nextClosure)
        );
    }

    public function testSecondsCommandIsNotDispatchedUntilFirstCommandIsComplete(): void
    {
        $secondCommandDispatched = false;

        $next2 = static function () use (&$secondCommandDispatched) {
            if (!$secondCommandDispatched) {
                throw new Exception('Second command was executed before the first completed!');
            }
        };

        $next1 = function () use (&$secondCommandDispatched, $next2) {
            $this->lockingMiddleware->execute(null, $next2);
            $secondCommandDispatched = true;
        };

        $this->lockingMiddleware->execute(null, $next1);
        self::assertTrue(true); // We made it through!
    }

    public function testTheReturnValueOfTheFirstCommandIsGivenBack(): void
    {
        $next2 = static function () {
            return 'second-payload';
        };

        $next1 = function () use ($next2) {
            $this->lockingMiddleware->execute(null, $next2);
            return 'first-payload';
        };

        // Only the return value of the first command should be returned
        self::assertEquals(
            'first-payload',
            $this->lockingMiddleware->execute(null, $next1)
        );
    }

    public function testTheCorrectSubCommandIsGivenToTheNextCallable(): void
    {
        $secondCommand = new CompleteTaskCommand();

        $next2 = static function ($command) use ($secondCommand) {
            if ($command !== $secondCommand) {
                throw new Exception('Received incorrect command: ' . get_class($command));
            }
        };

        $next1 = function () use ($next2, $secondCommand) {
            $this->lockingMiddleware->execute($secondCommand, $next2);
        };

        $this->lockingMiddleware->execute(null, $next1);
        self::assertTrue(true); // we made it through!
    }

    public function testExceptionsDoNotLeaveTheCommandBusLocked(): void
    {
        $next = static function () {
            throw new Exception();
        };

        try {
            $this->lockingMiddleware->execute(new AddTaskCommand(), $next);
        } catch (Exception $e) {
        }

        $next2 = static function () use (&$executed) {
            return true;
        };
        self::assertTrue(
            $this->lockingMiddleware->execute(new AddTaskCommand(), $next2),
            'Second command was not executed'
        );
    }

    public function testExceptionsDoNotLeaveQueuedCommandsInTheBus(): void
    {
        $next = function () {

            $this->lockingMiddleware->execute(
                new CompleteTaskCommand(),
                static function () {
                    throw new Exception('This $next gets queued but should never be triggered');
                }
            );

            throw new Exception('Exit out, thus dropping the queue');
        };

        // Now we create an error and suppress the exception...
        try {
            $this->lockingMiddleware->execute(new AddTaskCommand(), $next);
        } catch (Exception $e) {
        }

        // Then check the next command is executed but the queued one never is
        $next2 = static function () use (&$executed) {
            return true;
        };
        self::assertTrue(
            $this->lockingMiddleware->execute(new AddTaskCommand(), $next2),
            'Next pending command was not executed'
        );
    }

    public function testExceptionsAreAllowedToBubbleUp(): void
    {
        $next = static function () {
            throw new LogicException('Exit out, thus dropping the queue');
        };

        $this->expectException(LogicException::class);
        $this->lockingMiddleware->execute(new AddTaskCommand(), $next);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
