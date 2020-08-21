<?php

namespace League\Tactician\Tests\Exception;

use League\Tactician\Exception\CanNotDetermineCommandNameException;
use Mockery;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use stdClass;

/**
 * Class CanNotDetermineCommandNameExceptionTest
 *
 * @package League\Tactician\Tests\Exception
 */
class CanNotDetermineCommandNameExceptionTest extends TestCase
{
    /**
     * @dataProvider provideAnyTypeOfCommand
     *
     * @param $command
     */
    public function testForAnyTypeOfCommand($command): void
    {
        $exception = CanNotDetermineCommandNameException::forCommand($command);
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
