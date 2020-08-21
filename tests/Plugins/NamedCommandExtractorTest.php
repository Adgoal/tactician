<?php

namespace League\Tactician\Tests\Plugins;

use League\Tactician\Plugins\NamedCommand\NamedCommandExtractor;
use League\Tactician\Tests\Fixtures\Command\CommandWithAName;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use League\Tactician\Exception\CanNotDetermineCommandNameException;

/**
 * Class NamedCommandExtractorTest
 *
 * @package League\Tactician\Tests\Plugins
 */
class NamedCommandExtractorTest extends TestCase
{
    /**
     * @var NamedCommandExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        $this->extractor = new NamedCommandExtractor();
    }

    public function testExtractsNameFromANamedCommand(): void
    {
        self::assertEquals(
            'commandName',
            $this->extractor->extract(new CommandWithAName)
        );
    }

    public function testThrowsExceptionForNonNamedCommand(): void
    {
        $this->expectException(CanNotDetermineCommandNameException::class);
        $this->expectExceptionMessage('Could not determine command name of stdClass');

        $this->extractor->extract(new stdClass);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
