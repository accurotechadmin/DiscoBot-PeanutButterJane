<?php

declare(strict_types=1);

$env = static function (string $name, string $default = ''): string {
    $value = getenv($name);

    return $value === false ? $default : $value;
};

return [
    'token' => $env('DISCORD_BOT_TOKEN'),
    'prefix' => $env('BOT_PREFIX', '!bot'),
    'timezone' => $env('BOT_TIMEZONE', 'America/Toronto'),
    'env' => $env('APP_ENV', 'local'),
    'log_level' => $env('LOG_LEVEL', 'debug'),
    'logging' => [
        'file_enabled' => $env('LOG_FILE_ENABLED', 'true'),
        'directory' => $env('LOG_FILE_DIR', __DIR__ . '/../storage/logs'),
    ],
    'interactions' => [
        'prefix_commands' => $env('BOT_ENABLE_PREFIX_COMMANDS', 'true'),
        'slash_commands' => $env('BOT_ENABLE_SLASH_COMMANDS', 'false'),
        'mention_commands' => $env('BOT_ENABLE_MENTION_COMMANDS', 'false'),
        'dm_commands' => $env('BOT_ENABLE_DM_COMMANDS', 'false'),
    ],
    'rate_limit' => [
        'max_attempts' => $env('BOT_RATE_LIMIT_MAX_ATTEMPTS', '5'),
        'window_seconds' => $env('BOT_RATE_LIMIT_WINDOW_SECONDS', '10'),
    ],
];
