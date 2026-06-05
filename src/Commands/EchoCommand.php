<?php

declare(strict_types=1);

namespace App\Commands;

use App\CommandContext;

final class EchoCommand implements CommandInterface
{
    public function execute(CommandContext $context): string
    {
        $text = trim(implode(' ', $context->arguments()));

        if ($text === '') {
            return sprintf('Nothing to echo. Try `%s`.', CommandUsage::format($context->prefix(), 'echo hello world'));
        }

        return $text;
    }

    public function description(): string
    {
        return 'Echo command arguments back to Discord.';
    }

    public function usage(string $prefix): string
    {
        return CommandUsage::format($prefix, 'echo <message>');
    }
}
