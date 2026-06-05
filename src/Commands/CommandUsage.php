<?php

declare(strict_types=1);

namespace App\Commands;

final class CommandUsage
{
    public static function format(string $prefix, string $command): string
    {
        if ($prefix === '/') {
            return sprintf('/%s', $command);
        }

        if ($prefix === '') {
            return $command;
        }

        return sprintf('%s %s', $prefix, $command);
    }
}
