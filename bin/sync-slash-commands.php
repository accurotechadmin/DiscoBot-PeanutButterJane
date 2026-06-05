#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\CommandRouter;
use App\ConfigValidator;
use App\ConsoleLogger;
use App\SlashCommandSynchronizer;

$autoload = __DIR__ . '/../vendor/autoload.php';

if (!is_file($autoload)) {
    fwrite(STDERR, sprintf(
        '[%s] ERROR: Missing Composer dependencies. Run `composer install` before synchronizing slash commands.%s',
        date('Y-m-d H:i:s'),
        PHP_EOL,
    ));
    exit(1);
}

require $autoload;

$loadEnv = static function (string $path): void {
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if ($name === '' || getenv($name) !== false) {
            continue;
        }

        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"'))
            || (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
};

$loadEnv(__DIR__ . '/../.env');

$config = [
    'bot' => require __DIR__ . '/../config/bot.php',
    'commands' => [
        'registry' => require __DIR__ . '/../config/commands.php',
    ],
];

try {
    $botConfig = $config['bot'];
    $commandRegistry = $config['commands']['registry'];
    ConfigValidator::validateStartupConfig($botConfig, $commandRegistry);

    $loggingConfig = is_array($botConfig['logging'] ?? null) ? $botConfig['logging'] : [];
    $logDirectory = ConfigValidator::booleanValue($loggingConfig['file_enabled'] ?? 'true', 'LOG_FILE_ENABLED')
        ? (string) ($loggingConfig['directory'] ?? (__DIR__ . '/../storage/logs'))
        : null;
    $log = new ConsoleLogger((string) ($botConfig['log_level'] ?? 'debug'), fileDirectory: $logDirectory);
    $router = new CommandRouter($commandRegistry, $log);
    $synchronizer = new SlashCommandSynchronizer($config, $router, $log);
    $synchronizer->sync();
} catch (Throwable $exception) {
    fwrite(STDERR, sprintf('[%s] ERROR: %s%s', date('Y-m-d H:i:s'), $exception->getMessage(), PHP_EOL));
    exit(1);
}
