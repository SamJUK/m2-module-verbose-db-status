<?php declare(strict_types=1);

namespace SamJUK\VerboseDBStatus\Test\Unit\Plugin;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SamJUK\VerboseDBStatus\Plugin\UpToDateDeclarativeSchema as UpToDateDeclarativeSchemaPlugin;
use SamJUK\VerboseDBStatus\Model\Verbosity;
use SamJUK\VerboseDBStatus\Model\IdentifySchemaChanges;
use Magento\Framework\Setup\Declaration\Schema\UpToDateDeclarativeSchema as BaseUpToDateDeclarativeSchema;

class UpToDateDeclarativeSchemaTest extends TestCase
{
    private const FIXTURE_NOT_UP_TO_DATE_MESSAGE = "Declarative Schema is not up to date\n
    Run 'setup:upgrade' to update your DB schema and data.";

    /** @var BaseUpToDateDeclarativeSchema&MockObject */
    private $baseUpToDateDeclarativeSchema;
    /** @var Verbosity&MockObject */
    private $verbosity;
    private UpToDateDeclarativeSchemaPlugin $upToDateDeclarativeSchemaPlugin;
    /** @var IdentifySchemaChanges&MockObject */
    private $identifySchemaChanges;

    public function setUp(): void
    {
        $this->verbosity = $this->createMock(Verbosity::class);
        
        $this->identifySchemaChanges = $this->createMock(IdentifySchemaChanges::class);
        $this->identifySchemaChanges->method('getChangeLog')->willReturn('Mock Changelog');

        $this->baseUpToDateDeclarativeSchema = $this->createMock(BaseUpToDateDeclarativeSchema::class);
        $this->upToDateDeclarativeSchemaPlugin = new UpToDateDeclarativeSchemaPlugin(
            $this->identifySchemaChanges,
            $this->verbosity
        );
    }

    public function testPluginReturnsNonVerboseContent(): void
    {
        $this->verbosity->method('isVerbose')
            ->willReturn(false);

        $this->verbosity->method('isExtraVerbose')
            ->willReturn(false);

        $result = $this->upToDateDeclarativeSchemaPlugin->afterGetNotUpToDateMessage(
            $this->baseUpToDateDeclarativeSchema,
            self::FIXTURE_NOT_UP_TO_DATE_MESSAGE
        );

        $this->assertEquals(trim($result), self::FIXTURE_NOT_UP_TO_DATE_MESSAGE);
    }

    public function testPluginReturnsVerboseContent(): void
    {
        $this->verbosity->method('isVerbose')
            ->willReturn(true);

        $this->verbosity->method('isExtraVerbose')
            ->willReturn(false);

        $result = $this->upToDateDeclarativeSchemaPlugin->afterGetNotUpToDateMessage(
            $this->baseUpToDateDeclarativeSchema,
            self::FIXTURE_NOT_UP_TO_DATE_MESSAGE
        );

        $this->assertMatchesRegularExpression("/Mock Changelog/", $result);
    }
}
