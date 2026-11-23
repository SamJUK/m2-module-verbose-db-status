<?php declare(strict_types=1);

namespace SamJUK\VerboseDBStatus\Test\Unit\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SamJUK\VerboseDBStatus\Model\OperationTarget;
use SamJUK\VerboseDBStatus\Model\Verbosity;
use SamJUK\VerboseDBStatus\Model\OperationChange;

use Magento\Framework\Setup\Declaration\Schema\Dto\Index;
use Magento\Framework\Setup\Declaration\Schema\Dto\Table;
use Magento\Framework\Setup\Declaration\Schema\Dto\Column;
use Magento\Framework\Setup\Declaration\Schema\Dto\Constraint;

class OperationTargetTest extends TestCase
{
    /** @var Verbosity&MockObject */
    private $verbosity;
    /** @var \SamJUK\VerboseDBStatus\Model\OperationChangeFactory&MockObject */
    private $operationChangeFactory;

    public function setUp(): void
    {
        // phpcs:disable
        if (!class_exists('\SamJUK\VerboseDBStatus\Model\OperationChangeFactory')) {
            eval('namespace SamJUK\VerboseDBStatus\Model;class OperationChangeFactory {public function create() { return; }}');
        }
        // phpcs:enable

        $this->verbosity = $this->createMock(Verbosity::class);
        $this->operationChangeFactory = $this->createMock('\SamJUK\VerboseDBStatus\Model\OperationChangeFactory');
        $this->operationChangeFactory->method('create')->willReturn(new OperationChange);
    }

    public function testChangesDoesNotContainData(): void
    {
        $table = $this->getTableDataElement('my_table');
        $target = new OperationTarget($this->verbosity, $this->operationChangeFactory);
        $target->setOldData($table);
        $target->setNewData($table);

        $this->assertEmpty(array_filter($target->getChanges(), function ($change) {
            return $change->getKey() === 'data';
        }));
    }

    public function testVerboseChangesDoesContainData(): void
    {
        $this->verbosity->method('isExtraVerbose')->willReturn(true);

        $table = $this->getTableDataElement('my_table');
        $target = new OperationTarget($this->verbosity, $this->operationChangeFactory);
        $target->setOldData($table);
        $target->setNewData($table);

        $this->assertNotEmpty(array_filter($target->getChanges(), function ($change) {
            return $change->getKey() === 'data';
        }));
    }

    public function testGetTitleForTableOperation(): void
    {
        $table = $this->getTableDataElement('my_table');

        $target = new OperationTarget($this->verbosity, $this->operationChangeFactory);
        $target->setOldData($table);
        $target->setNewData($table);

        $expectedTitle = sprintf(
            '%s (%s %s)',
            $table->getName(),
            $table->getType(),
            $table->getElementType()
        );

        $this->assertEquals(
            $expectedTitle,
            $target->getTitle()
        );
    }

    public function testGetTitleForColumnOperation(): void
    {
        $column = $this->getColumnDataElement('entity_id', 'int');

        $target = new OperationTarget($this->verbosity, $this->operationChangeFactory);
        $target->setOldData($column);
        $target->setNewData($column);

        $expectedTitle = sprintf(
            '%s (%s %s %s)',
            $column->getName(),
            $column->getType(),
            $column->getElementType(),
            $column->getTable()->getName()
        );

        $this->assertEquals(
            $expectedTitle,
            $target->getTitle()
        );
    }

    public function testGetTitleForConstraintOperation(): void
    {
        $column = $this->getConstraintDataElement('MY_TABLE_ENTITY_ID_ATTRIBUTE_ID_STORE_ID', 'unique');

        $target = new OperationTarget($this->verbosity, $this->operationChangeFactory);
        $target->setOldData($column);
        $target->setNewData($column);

        $expectedTitle = sprintf(
            '%s (%s %s %s)',
            $column->getName(),
            $column->getType(),
            $column->getElementType(),
            $column->getTable()->getName()
        );

        $this->assertEquals(
            $expectedTitle,
            $target->getTitle()
        );
    }

    public function testGetTitleForIndexOperation(): void
    {
        $index = $this->getIndexDataElement('MY_TABLE_STORE_ID');

        $target = new OperationTarget($this->verbosity, $this->operationChangeFactory);
        $target->setOldData($index);
        $target->setNewData($index);

        $expectedTitle = sprintf(
            '%s (%s %s %s)',
            $index->getName(),
            $index->getType(),
            $index->getElementType(),
            $index->getTable()->getName()
        );

        $this->assertEquals(
            $expectedTitle,
            $target->getTitle()
        );
    }

    /**
     * @return Table&MockObject
     */
    private function getTableDataElement(string $name)
    {
        $mock = $this->createMock(Table::class);
        $mock->method('getDiffSensitiveParams')->willReturn([]);
        $mock->method('getName')->willReturn($name);
        $mock->method('getType')->willReturn('table');
        $mock->method('getElementType')->willReturn(Table::TYPE);
        return $mock;
    }

    /**
     * @return Column&MockObject
     */
    private function getColumnDataElement(string $name, string $type)
    {
        $mockTable = $this->getTableDataElement('my_table');

        $mock = $this->createMock(Column::class);
        $mock->method('getName')->willReturn($name);
        $mock->method('getType')->willReturn($type);
        $mock->method('getElementType')->willReturn(Column::TYPE);
        $mock->method('getTable')->willReturn($mockTable);
        return $mock;
    }

    /**
     * @return Constraint&MockObject
     */
    private function getConstraintDataElement(string $name, string $type)
    {
        $mockTable = $this->getTableDataElement('my_table');

        $mock = $this->createMock(Constraint::class);
        $mock->method('getName')->willReturn($name);
        $mock->method('getType')->willReturn($type);
        $mock->method('getElementType')->willReturn(Constraint::TYPE);
        $mock->method('getTable')->willReturn($mockTable);
        return $mock;
    }

    /**
     * @return Index&MockObject
     */
    private function getIndexDataElement(string $name)
    {
        $mockTable = $this->getTableDataElement('my_table');

        $mock = $this->createMock(Index::class);
        $mock->method('getName')->willReturn($name);
        $mock->method('getType')->willReturn(Index::TYPE);
        $mock->method('getElementType')->willReturn(Index::TYPE);
        $mock->method('getTable')->willReturn($mockTable);
        return $mock;
    }
}
