<?php

namespace League\Tactician\Tests\Handler;

use League\Tactician\Exception\CanNotInvokeHandlerException;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;
use League\Tactician\Handler\Locator\HandlerLocator;
use League\Tactician\Handler\MethodNameInflector\MethodNameInflector;
use League\Tactician\Tests\Fixtures\Command\CompleteTaskCommand;
use League\Tactician\Tests\Fixtures\Handler\DynamicMethodsHandler;
use League\Tactician\Tests\Fixtures\Handler\ConcreteMethodsHandler;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Mockery;

/**
 * Class CommandHandlerMiddlewareTest
 *
 * @package League\Tactician\Tests\Handler
 */
class CommandHandlerMiddlewareTest extends TestCase
{
    /**
     * @var CommandHandlerMiddleware
     */
    private $middleware;

    /**
     * @var CommandNameExtractor|Mockery\MockInterface
     */
    private $commandNameExtractor;

    /**
     * @var HandlerLocator|Mockery\MockInterface
     */
    private $handlerLocator;

    /**
     * @var MethodNameInflector|Mockery\MockInterface
     */
    private $methodNameInflector;

    protected function setUp(): void
    {
        $this->commandNameExtractor = Mockery::mock(CommandNameExtractor::class);
        $this->handlerLocator = Mockery::mock(HandlerLocator::class);
        $this->methodNameInflector = Mockery::mock(MethodNameInflector::class);

        $this->middleware = new CommandHandlerMiddleware(
            $this->commandNameExtractor,
            $this->handlerLocator,
            $this->methodNameInflector
        );
    }

    public function testHandlerIsExecuted(): void
    {
        $command = new CompleteTaskCommand();

        $handler = Mockery::mock(ConcreteMethodsHandler::class);
        $handler
            ->shouldReceive('handleCompleteTaskCommand')
            ->with($command)
            ->once()
            ->andReturn('a-return-value');

        $this->methodNameInflector
            ->shouldReceive('inflect')
            ->withArgs([$command, $handler])
            ->andReturn('handleCompleteTaskCommand');

        $this->handlerLocator
            ->shouldReceive('getHandlerForCommand')
            ->with(CompleteTaskCommand::class)
            ->andReturn($handler);

        $this->commandNameExtractor
            ->shouldReceive('extract')
            ->with($command)
            ->andReturn(CompleteTaskCommand::class);

        self::assertEquals('a-return-value', $this->middleware->execute($command, $this->mockNext()));
    }

    public function testMissingMethodOnHandlerObjectIsDetected(): void
    {
        $command = new CompleteTaskCommand();

        $this->methodNameInflector
            ->shouldReceive('inflect')
            ->andReturn('someMethodThatDoesNotExist');

        $this->handlerLocator
            ->shouldReceive('getHandlerForCommand')
            ->andReturn(new stdClass);

        $this->commandNameExtractor
            ->shouldReceive('extract')
            ->with($command);

        $this->expectException(CanNotInvokeHandlerException::class);
        $this->middleware->execute($command, $this->mockNext());
    }

    public function testDynamicMethodNamesAreSupported(): void
    {
        $command = new CompleteTaskCommand();
        $handler = new DynamicMethodsHandler();

        $this->methodNameInflector
            ->shouldReceive('inflect')
            ->andReturn('someHandlerMethod');

        $this->handlerLocator
            ->shouldReceive('getHandlerForCommand')
            ->andReturn($handler);

        $this->commandNameExtractor
            ->shouldReceive('extract')
            ->with($command);

        $this->middleware->execute($command, $this->mockNext());

        self::assertEquals(
            ['someHandlerMethod'],
            $handler->getMethodsInvoked()
        );
    }

    public function testClosuresCanBeInvoked(): void
    {
        $command = new CompleteTaskCommand();
        $closureWasExecuted = false;
        $handler = static function () use (&$closureWasExecuted) {
            $closureWasExecuted = true;
        };

        $this->methodNameInflector
            ->shouldReceive('inflect')
            ->andReturn('__invoke');

        $this->handlerLocator
            ->shouldReceive('getHandlerForCommand')
            ->andReturn($handler);

        $this->commandNameExtractor
            ->shouldReceive('extract')
            ->with($command);

        $this->middleware->execute($command, $this->mockNext());

        self::assertTrue($closureWasExecuted);
    }

    /**
     * @return callable
     */
    protected function mockNext(): callable
    {
        return static function () {
            throw new LogicException('Middleware fell through to next callable, this should not happen in the test.');
        };
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
