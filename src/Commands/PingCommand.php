<?php

declare(strict_types=1);

namespace App\Commands;

use App\CommandContext;

final class PingCommand implements CommandInterface
{
    public function execute(CommandContext $context): string
    {
        return 'Pong!';
    }

    public function description(): string
    {
        return 'Check that the bot is online.';
    }

    public function usage(string $prefix): string
    {
        return CommandUsage::format($prefix, 'ping');
    }
}
