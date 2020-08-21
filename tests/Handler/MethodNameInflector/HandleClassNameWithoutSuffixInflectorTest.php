<?php

namespace League\Tactician\Tests\Handler\MethodNameInflector;

use League\Tactician\Handler\MethodNameInflector\HandleClassNameWithoutSuffixInflector;
use League\Tactician\Tests\Fixtures\Command\CompleteTaskCommand;
use League\Tactician\Tests\Fixtures\Handler\ConcreteMethodsHandler;
use DateTime;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class HandleClassNameWithoutSuffixInflectorTest
 *
 * @package League\Tactician\Tests\Handler\MethodNameInflector
 */
class HandleClassNameWithoutSuffixInflectorTest extends TestCase
{
    /**
     * @var HandleClassNameWithoutSuffixInflector
     */
    private $inflector;

    /**
     * @var object
     */
    private $mockHandler;

    protected function setUp(): void
    {
        $this->inflector = new HandleClassNameWithoutSuffixInflector();
        $this->handler = new ConcreteMethodsHandler();
    }

    public function testRemovesCommandSuffixFromClasses(): void
    {
        $command = new CompleteTaskCommand();

        self::assertEquals(
            'handleCompleteTask',
            $this->inflector->inflect($command, $this->mockHandler)
        );
    }

    public function testDoesNotChangeClassesWithoutSuffix(): void
    {
        self::assertEquals(
            'handleDateTime',
            $this->inflector->inflect(new DateTime(), $this->mockHandler)
        );
    }

    public function testRemovesCustomSuffix(): void
    {
        $inflector = new HandleClassNameWithoutSuffixInflector('Time');

        self::assertEquals(
            'handleDate',
            $inflector->inflect(new DateTime(), $this->mockHandler)
        );
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
