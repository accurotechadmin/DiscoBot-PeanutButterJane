<?php

declare(strict_types=1);

namespace App\Commands;

use App\CommandContext;

final class HelpCommand implements CommandInterface
{
    public function execute(CommandContext $context): string
    {
        $commands = $context->config()['commands']['metadata'] ?? [];
        $lines = ['Available commands:'];

        foreach ($commands as $metadata) {
            if (!is_array($metadata)) {
                continue;
            }

            $usage = isset($metadata['usage']) ? (string) $metadata['usage'] : '';
            $description = isset($metadata['description']) ? (string) $metadata['description'] : '';

            if ($usage === '') {
                continue;
            }

            $aliasText = $this->formatAliases($metadata['aliases'] ?? []);
            $lines[] = sprintf('- `%s` — %s%s', $usage, $description, $aliasText);
        }

        return implode("\n", $lines);
    }

    /** @param mixed $aliases */
    private function formatAliases(mixed $aliases): string
    {
        if (!is_array($aliases) || $aliases === []) {
            return '';
        }

        $aliasList = array_values(array_filter(array_map(
            static fn (mixed $alias): string => trim((string) $alias),
            $aliases,
        )));

        if ($aliasList === []) {
            return '';
        }

        return sprintf(' (aliases: `%s`)', implode('`, `', $aliasList));
    }

    public function description(): string
    {
        return 'List available commands.';
    }

    public function usage(string $prefix): string
    {
        return CommandUsage::format($prefix, 'help');
    }
}
