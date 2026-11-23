<?php declare(strict_types=1);

namespace SamJUK\VerboseDBStatus\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use SamJUK\VerboseDBStatus\Model\Verbosity;

class VerbosityTest extends TestCase
{

    public function testIsVerbose(): void
    {
        $verbosity = new Verbosity();

        unset($_ENV['SHELL_VERBOSITY']);
        $this->assertFalse($verbosity->isVerbose());

        $_ENV['SHELL_VERBOSITY'] = Verbosity::VERBOSE;
        $this->assertTrue($verbosity->isVerbose());

        $_ENV['SHELL_VERBOSITY'] = Verbosity::EXTRA_VERBOSE;
        $this->assertTrue($verbosity->isVerbose());
    }

    public function testIsExtraVerbose(): void
    {
        $verbosity = new Verbosity();

        unset($_ENV['SHELL_VERBOSITY']);
        $this->assertFalse($verbosity->isExtraVerbose());

        $_ENV['SHELL_VERBOSITY'] = Verbosity::VERBOSE;
        $this->assertFalse($verbosity->isExtraVerbose());

        $_ENV['SHELL_VERBOSITY'] = Verbosity::EXTRA_VERBOSE;
        $this->assertTrue($verbosity->isExtraVerbose());
    }
}
