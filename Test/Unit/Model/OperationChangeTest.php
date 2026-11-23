<?php declare(strict_types=1);

namespace SamJUK\VerboseDBStatus\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use SamJUK\VerboseDBStatus\Model\OperationChange;

class OperationChangeTest extends TestCase
{

    public function testToString(): void
    {
        $changeKey = 'comment';
        $changeOld = 'my table';
        $changeNew = 'My Table';

        $change = new OperationChange();
        $change->setKey($changeKey);
        $change->setOldValue($changeOld);
        $change->setNewValue($changeNew);

        $expectedString = sprintf(
            "%s%s: '%s' -> '%s'",
            str_repeat(' ', OperationChange::INDENT),
            $changeKey,
            $changeOld,
            $changeNew,
        );

        $this->assertEquals($expectedString, $change);
    }
}
