<?php

namespace League\Tactician\Tests\Fixtures\Command;

use League\Tactician\Plugins\NamedCommand\NamedCommand;

/**
 * Class CommandWithAName
 *
 * @package League\Tactician\Tests\Fixtures\Command
 */
class CommandWithAName implements NamedCommand
{
    public function getCommandName(): string
    {
        return 'commandName';
    }
}
