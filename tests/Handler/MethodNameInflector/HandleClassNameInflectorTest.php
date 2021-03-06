<?php

namespace League\Tactician\Tests\Handler\MethodNameInflector;

use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use League\Tactician\Tests\Fixtures\Command\CompleteTaskCommand;
use League\Tactician\Tests\Fixtures\Handler\ConcreteMethodsHandler;
use CommandWithoutNamespace;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class HandleClassNameInflectorTest
 *
 * @package League\Tactician\Tests\Handler\MethodNameInflector
 */
class HandleClassNameInflectorTest extends TestCase
{
    /**
     * @var HandleClassNameInflector
     */
    private $inflector;

    /**
     * @var object
     */
    private $mockHandler;

    protected function setUp(): void
    {
        $this->inflector = new HandleClassNameInflector();
        $this->handler = new ConcreteMethodsHandler();
    }

    public function testHandlesClassesWithoutNamespace(): void
    {
        $command = new CommandWithoutNamespace();

        self::assertEquals(
            'handleCommandWithoutNamespace',
            $this->inflector->inflect($command, $this->mockHandler)
        );
    }

    public function testHandlesNamespacedClasses(): void
    {
        $command = new CompleteTaskCommand();

        self::assertEquals(
            'handleCompleteTaskCommand',
            $this->inflector->inflect($command, $this->mockHandler)
        );
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
