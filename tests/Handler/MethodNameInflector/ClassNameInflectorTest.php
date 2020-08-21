<?php

namespace League\Tactician\Tests\Handler\MethodNameInflector;

use League\Tactician\Handler\MethodNameInflector\ClassNameInflector;
use League\Tactician\Tests\Fixtures\Command\CompleteTaskCommand;
use League\Tactician\Tests\Fixtures\Handler\ConcreteMethodsHandler;
use CommandWithoutNamespace;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class ClassNameInflectorTest
 *
 * @package League\Tactician\Tests\Handler\MethodNameInflector
 */
class ClassNameInflectorTest extends TestCase
{
    /**
     * @var ClassNameInflector
     */
    private $inflector;

    /**
     * @var object
     */
    private $mockHandler;

    protected function setUp(): void
    {
        $this->inflector = new ClassNameInflector();
        $this->mockHandler = new ConcreteMethodsHandler();
    }

    public function testHandlesClassesWithoutNamespace(): void
    {
        $command = new CommandWithoutNamespace();

        self::assertEquals(
            'commandWithoutNamespace',
            $this->inflector->inflect($command, $this->mockHandler)
        );
    }

    public function testHandlesNamespacedClasses(): void
    {
        $command = new CompleteTaskCommand();

        self::assertEquals(
            'completeTaskCommand',
            $this->inflector->inflect($command, $this->mockHandler)
        );
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
