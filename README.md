# DiscordPHP Bot Skeleton

A lightweight starter application for a long-running PHP CLI Discord bot powered by [`team-reflex/discord-php`](https://github.com/discord-php/DiscordPHP).

This project is intentionally small: no Laravel, Symfony, database, queues, Docker, or web controllers. DiscordPHP owns the event loop, and the bot is started from the command line:

```bash
php bin/bot.php
```

## Documentation

For the mature documentation set, reading paths, technical references, component inventory, examples, maintainer reports, maintainer guides, and ADRs, see [`docs/README.md`](docs/README.md). For the complete path from a fresh GitHub checkout to a responding bot that is ready to extend, see [From GitHub to a Running Bot](docs/01-user-guides/from-github-to-running-bot.md).

## Requirements

- PHP 8.1.2 or newer
- Composer
- A Discord application with a bot token
- Discord's **Message Content Intent** enabled for message-based command paths such as prefix, mention, and DM commands

## Install dependencies

Install DiscordPHP and the development tools declared in `composer.json`:

```bash
composer install
```

This skeleton supports normal Composer project workflows. It does not require you to generate or commit a lock file before using the starter.

If you are creating a fresh project from scratch instead of cloning this skeleton, install equivalent packages with:

```bash
composer require team-reflex/discord-php
composer require --dev phpunit/phpunit
```

## Configure environment

Copy the example environment file:

```bash
cp .env.example .env
```

Then edit `.env` and add your Discord bot token:

```dotenv
DISCORD_BOT_TOKEN=your-token-here
BOT_PREFIX=!bot
BOT_TIMEZONE=America/Toronto
APP_ENV=local
LOG_LEVEL=debug
LOG_FILE_ENABLED=true
LOG_FILE_DIR=storage/logs
BOT_ENABLE_PREFIX_COMMANDS=true
BOT_ENABLE_SLASH_COMMANDS=false
BOT_ENABLE_MENTION_COMMANDS=false
BOT_ENABLE_DM_COMMANDS=false
```

Never commit `.env`; it is ignored by Git.

Configuration notes:

- `DISCORD_BOT_TOKEN` is required and must not be blank.
- `BOT_PREFIX` defaults to `!bot`; the effective value must not be blank and must not contain spaces.
- `BOT_TIMEZONE` defaults to `America/Toronto`; the effective value must be a valid PHP timezone identifier, such as `UTC`, `America/Toronto`, or `Europe/London`.
- `APP_ENV` is shown in the safe `settings` command output.
- `LOG_LEVEL` defaults to `debug`; the effective value controls console and structured file logging and accepts `debug`, `info`, `warning`, or `error`.
- `LOG_FILE_ENABLED` defaults to `true`; when enabled, emitted log records are also appended to daily structured JSON files.
- `LOG_FILE_DIR` defaults to `storage/logs`; daily files are named `bot-YYYY-MM-DD.json`.
- `BOT_ENABLE_PREFIX_COMMANDS` defaults to `true` to preserve the original `!bot` behavior.
- `BOT_ENABLE_SLASH_COMMANDS`, `BOT_ENABLE_MENTION_COMMANDS`, and `BOT_ENABLE_DM_COMMANDS` default to `false` and can be set to `true` independently. At least one interaction path must be enabled.
- `BOT_RATE_LIMIT_MAX_ATTEMPTS` defaults to `5`; use integer `0` to disable the basic per-user command rate limit, or a positive integer to enforce it.
- `BOT_RATE_LIMIT_WINDOW_SECONDS` defaults to `10`; it must be a positive integer and controls the basic rate-limit window.

Startup validation checks the token shape, prefix, timezone, environment, log level, logging settings, interaction-path toggles, rate-limit settings, command registry, aliases, and slash option metadata before DiscordPHP connects.

## Run the bot

Start the long-running DiscordPHP process from your terminal:

```bash
php bin/bot.php
```

Or use the Composer script:

```bash
composer bot
```

Slash command synchronization is separated from normal runtime startup. After enabling slash commands or changing `config/commands.php`, synchronize definitions explicitly:

```bash
composer sync-slash-commands
```

The bootstrap script loads Composer, reads `.env`, loads config files, validates startup settings, registers commands, prints concise startup messages, and starts the Discord event loop.

## Invite the bot to a server

At a high level:

1. Open the [Discord Developer Portal](https://discord.com/developers/applications).
2. Create or select your application.
3. Add a bot user under **Bot** and copy its token into `.env`.
4. Enable **Message Content Intent** under the bot settings.
5. Use **OAuth2 → URL Generator**.
6. Select the `bot` scope and permissions such as **Send Messages**, **Read Message History**, and **View Channels**. If slash commands will be enabled, also select the `applications.commands` scope.
7. Open the generated URL and invite the bot to your server.

## Interaction paths and intents

By default, this skeleton listens for text messages such as `!bot ping`. Discord treats message content as privileged data, so prefix, mention, and DM commands require **Message Content Intent** in the Discord Developer Portal.

The bot can also be configured to respond to slash commands, bot mentions, and direct messages. Slash command replies are ephemeral and visible only to the invoking user. Mention and prefix replies are public in the server channel. Direct-message replies stay in the one-to-one DM conversation. See [Interaction Paths](docs/01-user-guides/interaction-paths.md).

## Command parsing

With the default `BOT_PREFIX=!bot` and prefix commands enabled:

- Messages that do not start with `!bot` are ignored.
- Prefix-adjacent text like `!botping` is ignored so ordinary words are not mistaken for bot commands.
- A bare prefix (`!bot`) routes to the help command.
- Command names are case-insensitive, so `!bot ping` and `!bot PiNg` behave the same.
- Arguments are split on whitespace and passed to the command through `CommandContext::arguments()`.
- Unknown commands return a friendly Discord reply that suggests `!bot help`.
- Command aliases are registered in `config/commands.php`; for example, `!bot commands` routes to the help command.

## Example commands

With the default prefix, try:

```text
!bot ping
!bot time
!bot settings
!bot echo hello world
!bot help
!bot commands
```

Included commands:

- `!bot ping` — check that the bot is online.
- `!bot time` — show the current time in `BOT_TIMEZONE`.
- `!bot settings` — show a safe summary of the current prefix, timezone, and app environment.
- `!bot echo <message>` — demonstrate argument handling by echoing a message back.
- `!bot help` — list commands with usage, descriptions, and aliases.
- `!bot commands` — alias for `!bot help`.

Help output uses each command's `usage()` and `description()` methods, which keeps Discord text readable while making examples obvious for users.

## Project structure

```text
bin/bot.php                 CLI bootstrap and event-loop entrypoint
config/bot.php              Bot-level environment-backed settings
config/commands.php         Command registry and aliases
src/Bot.php                 DiscordPHP integration and enabled interaction listeners
src/CommandContext.php      Data object passed into commands
src/CommandRouter.php       Prefix/mention/DM parsing, aliases, slash dispatch, metadata
src/ConfigValidator.php     Startup token, prefix, timezone, log-level, and toggle validator
src/ConsoleLogger.php       Tiny LOG_LEVEL-aware console logger
src/ParsedCommand.php       Small parsed-command value object used by the router
src/Commands/*              Command interface, usage helper, and built-in commands
tests/*                     PHPUnit tests for parsing, commands, and config validation
storage/logs/.gitkeep       Keeps the structured log directory in Git; generated daily JSON logs are ignored
```

## Add a new command

1. Create a command class in `src/Commands` that implements `App\Commands\CommandInterface`.
2. Implement:
   - `execute(CommandContext $context): string` for the Discord reply.
   - `description(): string` for help text.
   - `usage(string $prefix): string` for the example shown in help.
3. Read command arguments with `$context->arguments()` if your command accepts user input.
4. Use `$context->discord()` or `$context->message()` only when a command truly needs DiscordPHP objects. They are available during normal bot execution, but may be `null` in direct command tests and `CommandRouter::routeContent()` tests so the suite can run without Discord network calls.
5. Register the command in `config/commands.php`. This file remains the source of command registration.

Example:

```php
<?php

declare(strict_types=1);

namespace App\Commands;

use App\CommandContext;

final class EchoCommand implements CommandInterface
{
    public function execute(CommandContext $context): string
    {
        $text = trim(implode(' ', $context->arguments()));

        return $text === ''
            ? sprintf('Nothing to echo. Try `%s`.', CommandUsage::format($context->prefix(), 'echo hello world'))
            : $text;
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
```

Then register it:

```php
use App\Commands\EchoCommand;

return [
    // ...
    'echo' => EchoCommand::class,
];
```

To add aliases without changing the command class, use the config array form:

```php
use App\Commands\HelpCommand;

return [
    // ...
    'help' => [
        'class' => HelpCommand::class,
        'aliases' => ['commands'],
    ],
];
```

Aliases route to the same command but are not duplicated as separate full entries in help output.

## Run tests and checks

This skeleton includes a small PHPUnit suite for command parsing, built-in command output, aliases, safe command exception handling, and config validation. Tests do not connect to Discord or make network calls.

```bash
composer test
```

Other lightweight quality scripts are available:

```bash
composer lint
composer check
```

`composer lint` runs PHP syntax checks over `bin`, `src`, `config`, and `tests`. `composer check` runs linting and then the PHPUnit suite. You can also run PHPUnit directly after `composer install`:

```bash
vendor/bin/phpunit
```

## Error handling and logging

Basic per-user rate limiting runs before command dispatch, applies across channels for the same user, and replies with a short cooldown message when a user exceeds the configured window. Command exceptions are caught by the router so a bad command does not crash the bot. Errors are logged concisely to the console with the command name and source location, and Discord receives a generic friendly error message without secrets, stack traces, or implementation details.

Startup validation happens before the Discord event loop begins. Missing tokens, blank prefixes, invalid timezone values, and invalid log levels fail fast with clear console errors.

`LOG_LEVEL` filters console output:

- `debug` shows all startup hints and messages.
- `info` shows normal startup and ready messages.
- `warning` shows warnings and errors.
- `error` shows only errors.

## Troubleshooting

- **`composer install` fails**: confirm PHP 8.1.2+ is installed, required PHP extensions are enabled, and Composer can reach Packagist/GitHub.
- **`php bin/bot.php` says Composer dependencies are missing**: run `composer install` from the project root, then start the bot again.
- **`DISCORD_BOT_TOKEN` is missing**: copy `.env.example` to `.env`, set `DISCORD_BOT_TOKEN`, and restart `php bin/bot.php`. The bot intentionally exits before connecting to Discord when the token is blank.
- **`BOT_PREFIX` is invalid**: set a non-empty prefix without spaces, such as `!bot`. Blank prefixes and prefixes like `! bot` are rejected during startup.
- **`BOT_TIMEZONE` is invalid**: use a PHP timezone identifier such as `UTC`, `America/Toronto`, or `Europe/London`. Startup validation rejects unknown values before the event loop starts.
- **`LOG_LEVEL` is invalid**: use `debug`, `info`, `warning`, or `error`.
- **Prefix commands are ignored in Discord**: enable **Message Content Intent** for your bot in the Discord Developer Portal, then restart the bot. Prefix commands such as `!bot ping` require access to message content.
- **`!botping` is ignored**: add whitespace after the prefix, such as `!bot ping`. Prefix-adjacent words are intentionally ignored.
