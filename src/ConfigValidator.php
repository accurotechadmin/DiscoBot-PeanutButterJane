<?php

declare(strict_types=1);

namespace App;

use App\Commands\CommandInterface;
use DateTimeZone;
use RuntimeException;

final class ConfigValidator
{
    private const ALLOWED_ENVIRONMENTS = ['local', 'testing', 'staging', 'production'];
    private const ALLOWED_LOG_LEVELS = ['debug', 'info', 'warning', 'error'];
    private const MAX_PREFIX_LENGTH = 32;
    private const COMMAND_NAME_PATTERN = '/^[a-z0-9_-]{1,32}$/';
    private const SLASH_OPTION_NAME_PATTERN = '/^[a-z0-9_-]{1,32}$/';
    private const ALLOWED_SLASH_OPTION_TYPES = [3, 4, 5, 6, 7, 8, 9, 10, 11];

    /** @param array<string, mixed> $botConfig */
    public static function validateBotConfig(array $botConfig): void
    {
        self::validateToken((string) ($botConfig['token'] ?? ''));
        self::validatePrefix((string) ($botConfig['prefix'] ?? '!bot'));
        self::validateTimezone((string) ($botConfig['timezone'] ?? 'UTC'));
        self::validateEnvironment((string) ($botConfig['env'] ?? 'local'));
        self::validateLogLevel((string) ($botConfig['log_level'] ?? 'debug'));
        self::validateLoggingConfig($botConfig['logging'] ?? []);
        self::validateInteractionToggles($botConfig['interactions'] ?? []);
        self::validateRateLimit($botConfig['rate_limit'] ?? []);
    }

    /**
     * @param array<string, mixed> $botConfig
     * @param array<string, mixed> $commandRegistry
     */
    public static function validateStartupConfig(array $botConfig, array $commandRegistry): void
    {
        self::validateBotConfig($botConfig);
        self::validateCommandRegistry($commandRegistry);
    }

    public static function validateToken(string $token): void
    {
        $trimmedToken = trim($token);

        if ($trimmedToken === '') {
            throw new RuntimeException('Missing DISCORD_BOT_TOKEN. Copy .env.example to .env and set your bot token before starting the bot.');
        }

        if ($trimmedToken !== $token || preg_match('/\s|[[:cntrl:]]/', $token) === 1) {
            throw new RuntimeException('Invalid DISCORD_BOT_TOKEN. Tokens must not contain whitespace or control characters.');
        }

        if (preg_match('/^[A-Za-z0-9._-]{20,}$/', $token) !== 1) {
            throw new RuntimeException('Invalid DISCORD_BOT_TOKEN. Set a Discord bot token value before starting the bot.');
        }
    }

    public static function validatePrefix(string $prefix): void
    {
        if (trim($prefix) === '') {
            throw new RuntimeException('Invalid BOT_PREFIX. Set BOT_PREFIX to a non-empty prefix such as !bot.');
        }

        if (trim($prefix) !== $prefix || preg_match('/\s/', $prefix) === 1) {
            throw new RuntimeException(sprintf('Invalid BOT_PREFIX "%s". Use a short non-empty prefix without spaces, such as !bot.', $prefix));
        }

        if (strlen($prefix) > self::MAX_PREFIX_LENGTH || preg_match('/[[:cntrl:]]/', $prefix) === 1) {
            throw new RuntimeException(sprintf('Invalid BOT_PREFIX "%s". Use %d or fewer printable characters.', $prefix, self::MAX_PREFIX_LENGTH));
        }
    }

    public static function validateTimezone(string $timezone): void
    {
        if (!in_array($timezone, DateTimeZone::listIdentifiers(), true)) {
            throw new RuntimeException(sprintf('Invalid BOT_TIMEZONE "%s". Use a valid PHP timezone identifier such as UTC or America/Toronto.', $timezone));
        }
    }

    public static function validateEnvironment(string $environment): void
    {
        $normalized = strtolower(trim($environment));

        if (!in_array($normalized, self::ALLOWED_ENVIRONMENTS, true)) {
            throw new RuntimeException(sprintf('Invalid APP_ENV "%s". Use one of: %s.', $environment, implode(', ', self::ALLOWED_ENVIRONMENTS)));
        }
    }

    public static function validateLogLevel(string $logLevel): void
    {
        if (!in_array(strtolower(trim($logLevel)), self::ALLOWED_LOG_LEVELS, true)) {
            throw new RuntimeException(sprintf('Invalid LOG_LEVEL "%s". Use one of: %s.', $logLevel, implode(', ', self::ALLOWED_LOG_LEVELS)));
        }
    }

    /** @param mixed $logging */
    public static function validateLoggingConfig(mixed $logging): void
    {
        if (!is_array($logging)) {
            throw new RuntimeException('Invalid logging settings. Expected a logging config array.');
        }

        self::booleanValue($logging['file_enabled'] ?? 'true', 'LOG_FILE_ENABLED');
        $directory = trim((string) ($logging['directory'] ?? 'storage/logs'));

        if ($directory === '' || preg_match('/[[:cntrl:]]/', $directory) === 1) {
            throw new RuntimeException('Invalid LOG_FILE_DIR. Use a non-empty printable directory path.');
        }
    }

    public static function booleanValue(mixed $value, string $name): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower(trim((string) $value));

        return match ($normalized) {
            '1', 'true', 'yes', 'on' => true,
            '0', 'false', 'no', 'off' => false,
            default => throw new RuntimeException(sprintf('Invalid %s "%s". Use true or false.', $name, (string) $value)),
        };
    }

    /** @param mixed $interactions */
    public static function validateInteractionToggles(mixed $interactions): void
    {
        if (!is_array($interactions)) {
            throw new RuntimeException('Invalid bot interaction settings. Expected an interactions config array.');
        }

        $enabledPaths = 0;

        foreach ([
            'BOT_ENABLE_PREFIX_COMMANDS' => $interactions['prefix_commands'] ?? 'true',
            'BOT_ENABLE_SLASH_COMMANDS' => $interactions['slash_commands'] ?? 'false',
            'BOT_ENABLE_MENTION_COMMANDS' => $interactions['mention_commands'] ?? 'false',
            'BOT_ENABLE_DM_COMMANDS' => $interactions['dm_commands'] ?? 'false',
        ] as $name => $value) {
            if (self::booleanValue($value, $name)) {
                $enabledPaths++;
            }
        }

        if ($enabledPaths === 0) {
            throw new RuntimeException('Invalid bot interaction settings. Enable at least one interaction path.');
        }
    }

    /** @param mixed $rateLimit */
    public static function validateRateLimit(mixed $rateLimit): void
    {
        if (!is_array($rateLimit)) {
            throw new RuntimeException('Invalid rate limit settings. Expected a rate_limit config array.');
        }

        $maxAttempts = self::integerValue($rateLimit['max_attempts'] ?? 5, 'BOT_RATE_LIMIT_MAX_ATTEMPTS');
        $windowSeconds = self::integerValue($rateLimit['window_seconds'] ?? 10, 'BOT_RATE_LIMIT_WINDOW_SECONDS');

        if ($maxAttempts < 0) {
            throw new RuntimeException('Invalid BOT_RATE_LIMIT_MAX_ATTEMPTS. Use 0 or a positive integer.');
        }

        if ($windowSeconds < 1) {
            throw new RuntimeException('Invalid BOT_RATE_LIMIT_WINDOW_SECONDS. Use a positive integer.');
        }
    }

    private static function integerValue(mixed $value, string $name): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (!is_string($value) || preg_match('/^-?\d+$/', trim($value)) !== 1) {
            throw new RuntimeException(sprintf('Invalid %s "%s". Use an integer value.', $name, (string) $value));
        }

        return (int) trim($value);
    }

    /** @param array<string, mixed> $commandRegistry */
    public static function validateCommandRegistry(array $commandRegistry): void
    {
        if ($commandRegistry === []) {
            throw new RuntimeException('Invalid command registry. Register at least one command.');
        }

        $commandNames = [];
        $aliases = [];

        foreach ($commandRegistry as $name => $entry) {
            $commandName = self::normalizeName((string) $name);
            self::validateCommandName($commandName, sprintf('command "%s"', (string) $name));

            if (isset($commandNames[$commandName]) || isset($aliases[$commandName])) {
                throw new RuntimeException(sprintf('Invalid command registry. Duplicate command or alias "%s".', $commandName));
            }

            $commandNames[$commandName] = true;
            $classOrInstance = $entry;
            $entryAliases = [];
            $slashOptions = [];

            if (is_array($entry)) {
                if (!array_key_exists('class', $entry)) {
                    throw new RuntimeException(sprintf('Invalid command registry entry "%s". Missing class.', $commandName));
                }

                $classOrInstance = $entry['class'];
                $entryAliases = $entry['aliases'] ?? [];
                $slashOptions = $entry['slash_options'] ?? [];
            }

            self::validateCommandClass($commandName, $classOrInstance);

            if (!is_array($entryAliases)) {
                throw new RuntimeException(sprintf('Invalid aliases for command "%s". Expected a list.', $commandName));
            }

            foreach ($entryAliases as $alias) {
                $normalizedAlias = self::normalizeName((string) $alias);
                self::validateCommandName($normalizedAlias, sprintf('alias "%s"', (string) $alias));

                if ($normalizedAlias === $commandName) {
                    throw new RuntimeException(sprintf('Invalid command registry. Alias "%s" duplicates its command name.', $normalizedAlias));
                }

                if (isset($commandNames[$normalizedAlias]) || isset($aliases[$normalizedAlias])) {
                    throw new RuntimeException(sprintf('Invalid command registry. Duplicate alias or command name "%s".', $normalizedAlias));
                }

                $aliases[$normalizedAlias] = $commandName;
            }

            self::validateSlashOptions($commandName, $slashOptions);
        }
    }

    private static function validateCommandName(string $name, string $label): void
    {
        if (preg_match(self::COMMAND_NAME_PATTERN, $name) !== 1) {
            throw new RuntimeException(sprintf('Invalid %s. Use 1-32 lowercase letters, numbers, underscores, or hyphens.', $label));
        }
    }

    private static function validateCommandClass(string $commandName, mixed $classOrInstance): void
    {
        if ($classOrInstance instanceof CommandInterface) {
            return;
        }

        if (!is_string($classOrInstance) || !class_exists($classOrInstance)) {
            throw new RuntimeException(sprintf('Invalid command registry entry "%s". Command class does not exist.', $commandName));
        }

        if (!is_subclass_of($classOrInstance, CommandInterface::class)) {
            throw new RuntimeException(sprintf('Invalid command registry entry "%s". Command class must implement %s.', $commandName, CommandInterface::class));
        }
    }

    /** @param mixed $slashOptions */
    private static function validateSlashOptions(string $commandName, mixed $slashOptions): void
    {
        if (!is_array($slashOptions)) {
            throw new RuntimeException(sprintf('Invalid slash options for command "%s". Expected a list.', $commandName));
        }

        $optionNames = [];

        foreach ($slashOptions as $option) {
            if (!is_array($option)) {
                throw new RuntimeException(sprintf('Invalid slash option for command "%s". Expected an option array.', $commandName));
            }

            $name = self::normalizeName((string) ($option['name'] ?? ''));
            $description = trim((string) ($option['description'] ?? ''));
            $type = (int) ($option['type'] ?? 3);

            if (preg_match(self::SLASH_OPTION_NAME_PATTERN, $name) !== 1) {
                throw new RuntimeException(sprintf('Invalid slash option name "%s" for command "%s".', (string) ($option['name'] ?? ''), $commandName));
            }

            if (isset($optionNames[$name])) {
                throw new RuntimeException(sprintf('Invalid slash options for command "%s". Duplicate option "%s".', $commandName, $name));
            }

            if ($description === '' || strlen($description) > 100 || preg_match('/[[:cntrl:]]/', $description) === 1) {
                throw new RuntimeException(sprintf('Invalid slash option description for command "%s" option "%s". Use 1-100 printable characters.', $commandName, $name));
            }

            if (!in_array($type, self::ALLOWED_SLASH_OPTION_TYPES, true)) {
                throw new RuntimeException(sprintf('Invalid slash option type "%d" for command "%s" option "%s".', $type, $commandName, $name));
            }

            $optionNames[$name] = true;
        }
    }

    private static function normalizeName(string $name): string
    {
        return strtolower(trim($name));
    }
}
