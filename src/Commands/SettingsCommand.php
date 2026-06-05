<?php

declare(strict_types=1);

namespace App\Commands;

use App\CommandContext;

final class SettingsCommand implements CommandInterface
{
    public function execute(CommandContext $context): string
    {
        $botConfig = $context->config()['bot'] ?? [];
        $prefix = is_array($botConfig) && isset($botConfig['prefix']) ? (string) $botConfig['prefix'] : $context->prefix();
        $timezone = is_array($botConfig) && isset($botConfig['timezone']) ? (string) $botConfig['timezone'] : 'UTC';
        $environment = is_array($botConfig) && isset($botConfig['env']) ? (string) $botConfig['env'] : 'unknown';

        return implode("\n", [
            'Bot settings:',
            sprintf('- Prefix: `%s`', $prefix),
            sprintf('- Timezone: `%s`', $timezone),
            sprintf('- Environment: `%s`', $environment),
        ]);
    }

    public function description(): string
    {
        return 'Show the current bot settings summary.';
    }

    public function usage(string $prefix): string
    {
        return CommandUsage::format($prefix, 'settings');
    }
}
