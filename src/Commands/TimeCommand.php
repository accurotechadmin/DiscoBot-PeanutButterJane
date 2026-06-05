<?php

declare(strict_types=1);

namespace App\Commands;

use App\CommandContext;
use DateTimeImmutable;
use DateTimeZone;

final class TimeCommand implements CommandInterface
{
    public function execute(CommandContext $context): string
    {
        $config = $context->config()['bot'] ?? [];
        $timezoneName = is_array($config) && isset($config['timezone']) ? (string) $config['timezone'] : 'UTC';
        $timezone = new DateTimeZone($timezoneName);
        $now = new DateTimeImmutable('now', $timezone);

        return sprintf('Current time in %s: %s', $timezoneName, $now->format('Y-m-d H:i:s T'));
    }

    public function description(): string
    {
        return 'Show the current configured bot time.';
    }

    public function usage(string $prefix): string
    {
        return CommandUsage::format($prefix, 'time');
    }
}
