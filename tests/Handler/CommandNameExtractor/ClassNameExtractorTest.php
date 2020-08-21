<?php

namespace League\Tactician\Tests\Handler\CommandNameExtractor;

use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class ClassNameExtractorTest
 *
 * @package League\Tactician\Tests\Handler\CommandNameExtractor
 */
class ClassNameExtractorTest extends TestCase
{
    /**
     * @var ClassNameExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        $this->extractor = new ClassNameExtractor();
    }

    public function testExtractsNameFromACommand(): void
    {
        self::assertEquals(
            'stdClass',
            $this->extractor->extract(new stdClass)
        );
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
