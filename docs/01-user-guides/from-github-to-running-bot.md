# From GitHub to a Running Bot

**Audience:** Users who want a complete path from a fresh GitHub checkout to a working Discord bot that is ready to extend.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../README.md`, `../../composer.json`, `../../.env.example`, `../../bin/bot.php`, `../../config/bot.php`, `../../config/commands.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../src/Commands/`, `../../tests/`
**Related docs:** [Quick start](quick-start.md), [Installation](installation.md), [Configuration](configuration.md), [Inviting the bot to Discord](inviting-the-bot-to-discord.md), [Running the bot](running-the-bot.md), [Using prefix commands](using-prefix-commands.md), [Adding a command](../04-extensibility/adding-a-command.md), [Testing new commands](../04-extensibility/testing-new-commands.md)

This guide answers two practical questions:

1. Does the repository work as a real Discord bot?
2. What is needed to go from a fresh GitHub checkout to a fully functional Discord bot that responds to enabled command paths and is ready to extend?

## Short answer

Yes, the repository is structured to work as a real DiscordPHP bot with prefix commands enabled by default and slash, mention, and DM paths available as opt-in interaction paths, but a fresh GitHub checkout will not run without setup. You must install Composer dependencies, provide a real Discord bot token, enable Discord's Message Content Intent, invite the bot to a Discord server with channel permissions, and keep the PHP CLI process running.

A fresh checkout is expected to need setup because:

- `../../bin/bot.php` exits if `vendor/autoload.php` is missing.
- `../../.env.example` intentionally contains an empty `DISCORD_BOT_TOKEN` placeholder.
- Discord application settings and server permissions live in Discord, not in this repository.
- The repository provides the CLI process, not an external process manager.

After setup, the intended confirmation is simple: start the bot, type `!bot ping` in Discord, and receive `Pong!`.

## What the codebase already provides

### Runtime entrypoint

The bot starts through either command:

```bash
php bin/bot.php
```

or:

```bash
composer bot
```

The bootstrap script in `../../bin/bot.php` checks for Composer autoloading, loads optional `.env` values, loads config, validates startup settings, creates the console logger and command router, constructs the Discord bot runtime, and starts DiscordPHP.

### Discord connection and event handling

`../../src/Bot.php` constructs the DiscordPHP client with:

- the configured bot token;
- DiscordPHP default intents, plus `MESSAGE_CONTENT` when prefix, mention, or DM commands are enabled;
- `DIRECT_MESSAGES` intent when DM commands are enabled;
- memory-conscious client options;
- a `ready` listener;
- a `MESSAGE_CREATE` listener only when at least one message-based path is enabled;
- slash command registration and listeners when slash commands are enabled;
- bot/self-message guards for message paths;
- public server replies, private DM replies, and ephemeral slash replies.

### Command routing

`../../src/CommandRouter.php` handles:

- command registration;
- alias registration;
- prefix parsing;
- mention parsing;
- unprefixed DM parsing;
- shared slash dispatch;
- case-insensitive command names;
- whitespace-split arguments;
- a bare-prefix-to-help fallback;
- unknown-command replies;
- help metadata injection;
- safe command exception handling.

### Built-in commands

The current built-in commands are registered in `../../config/commands.php`:

| User input | Behavior |
| --- | --- |
| `!bot ping` | Replies `Pong!`. |
| `!bot time` | Shows the current time in the configured timezone. |
| `!bot settings` | Shows a safe prefix, timezone, and environment summary. |
| `!bot echo <message>` | Echoes the provided arguments. |
| `!bot help` | Lists registered commands with usage, descriptions, and aliases. |
| `!bot commands` | Alias for `!bot help`. |

### Extension points

Commands live in `../../src/Commands/` and implement `App\Commands\CommandInterface`. Each command provides:

- `execute()` for the Discord reply;
- `description()` for help text;
- `usage()` for help examples.

New commands become active only after you add the command class and register it in `../../config/commands.php`.

## What is not included

Current behavior: slash commands are included as an opt-in interaction path. Set `BOT_ENABLE_SLASH_COMMANDS=true` to register configured command names and aliases as slash commands. Slash replies are ephemeral and visible only to the invoking user.

Current behavior: this repository does not include:

- a web server;
- HTTP controllers;
- Laravel or Symfony integration;
- a framework service container;
- a database;
- queue workers;
- log aggregation or monitoring;
- Docker-first deployment files;
- process supervision;
- production hosting automation.

**Future consideration:** adding any absent infrastructure feature should include source changes, tests or verification guidance, and documentation updates. Do not describe those features as existing behavior unless they are added to the repository.

## End-to-end setup guide

### 1. Clone the repository

```bash
git clone <your-repo-url>
cd <repo-directory>
```

Run commands from the repository root so Composer creates `vendor/autoload.php` where `../../bin/bot.php` expects it.

### 2. Confirm local prerequisites

Check PHP and Composer:

```bash
php -v
composer --version
```

You need:

- PHP 8.1.2 or newer;
- Composer;
- network access for Composer package installation;
- a Discord application with a bot token;
- Discord Message Content Intent enabled for the bot when prefix, mention, or DM paths are enabled.
- The `applications.commands` invite scope when slash commands are enabled.

The PHP version and package requirements are declared in `../../composer.json`.

### 3. Install Composer dependencies

```bash
composer install
```

This installs DiscordPHP and development tools declared in `../../composer.json`.

This step is mandatory. If dependencies are missing, `../../bin/bot.php` prints a missing Composer dependencies error and exits before loading the bot.

### 4. Create your environment file

```bash
cp .env.example .env
```

Edit `.env`:

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
BOT_RATE_LIMIT_MAX_ATTEMPTS=5
BOT_RATE_LIMIT_WINDOW_SECONDS=10
```

Never commit `.env`; it should contain your private bot token.

### 5. Understand the environment variables

| Variable | Required in `.env` for a real bot? | Purpose | Example |
| --- | --- | --- | --- |
| `DISCORD_BOT_TOKEN` | Yes | Token used by DiscordPHP to connect to Discord. | `your-token-here` |
| `BOT_PREFIX` | No, default exists | Prefix users type before commands. | `!bot` |
| `BOT_TIMEZONE` | No, default exists | Timezone used by the `time` command. | `UTC` |
| `APP_ENV` | No, default exists | Safe environment label shown by `settings`. | `local` |
| `LOG_LEVEL` | No, default exists | Minimum console log level. | `debug` |
| `BOT_ENABLE_PREFIX_COMMANDS` | No, default exists | Enables prefixed server message commands. | `true` |
| `BOT_ENABLE_SLASH_COMMANDS` | No, default exists | Enables slash command registration and ephemeral replies. | `false` |
| `BOT_ENABLE_MENTION_COMMANDS` | No, default exists | Enables public server mention commands. | `false` |
| `BOT_ENABLE_DM_COMMANDS` | No, default exists | Enables unprefixed DM commands. | `false` |

Startup validation checks that:

- `DISCORD_BOT_TOKEN` is not blank;
- `BOT_PREFIX` is not blank and contains no whitespace;
- `BOT_TIMEZONE` is a valid PHP timezone identifier;
- `LOG_LEVEL` is one of `debug`, `info`, `warning`, or `error`;
- interaction toggles are boolean-like values such as `true`, `false`, `1`, `0`, `yes`, `no`, `on`, or `off`.

### 6. Create a Discord application and bot user

In the Discord Developer Portal:

1. Create or select an application.
2. Add a bot user.
3. Copy the bot token.
4. Paste that token into `.env` as `DISCORD_BOT_TOKEN=...`.

The repository cannot create this Discord-side application for you.

### 7. Enable Message Content Intent

Prefix, mention, and DM paths use message text. With the default prefix path, users type commands such as:

```text
!bot ping
!bot help
```

Enable Message Content Intent for the bot application in the Discord Developer Portal when any message-based path is enabled, then restart the bot process after changing that setting.

`../../src/Bot.php` requests Message Content Intent in code, but the Discord application setting must also be enabled.

### 8. Invite the bot to a Discord server

Use the Discord Developer Portal's OAuth2 URL Generator.

Recommended minimum setup for this skeleton:

- Scope: `bot`. Add `applications.commands` too when slash commands are enabled.
- Permissions: View Channels, Send Messages, and Read Message History.

Open the generated invite URL and add the bot to a server where you have permission to invite bots.

If the bot works in one channel but not another, check channel-specific Discord permissions.

### 9. Run checks before starting the live bot

After `composer install`, run:

```bash
composer lint
composer test
composer check
```

`composer lint` checks PHP syntax. `composer test` runs PHPUnit. `composer check` runs linting and tests.

The test suite is offline: it exercises command output, routing, config validation, and logger filtering without connecting to Discord.

If `composer test` says `vendor/bin/phpunit` is missing, run `composer install` first.

### 10. Start the bot

```bash
php bin/bot.php
```

or:

```bash
composer bot
```

Expected startup flow:

1. Composer autoloading is loaded.
2. `.env` is loaded if present and readable.
3. `../../config/bot.php` and `../../config/commands.php` are loaded.
4. startup config is validated.
5. the console logger is created.
6. the command router is created.
7. DiscordPHP is constructed.
8. the process connects to Discord.
9. the bot logs that it is ready and listening for commands.

Keep this process running while you want the bot online.

### 11. Test the bot in Discord

With the default prefix, type these messages in a channel the bot can read and reply to:

```text
!bot ping
!bot time
!bot settings
!bot echo hello world
!bot help
!bot commands
```

Expected behavior:

| Message | Expected behavior |
| --- | --- |
| `!bot ping` | Replies `Pong!`. |
| `!bot time` | Replies with current time in `BOT_TIMEZONE`. |
| `!bot settings` | Shows prefix, timezone, and environment. |
| `!bot echo hello world` | Replies `hello world`. |
| `!bot help` | Lists available command usage and descriptions. |
| `!bot commands` | Routes to `help` through the configured alias. |

## How command parsing behaves

Assuming the default prefix is `!bot`, these messages route to commands:

```text
!bot ping
!bot PiNg
!bot echo hello world
!bot
```

These messages are ignored:

```text
hello
!other ping
!botping
```

Important parser details:

- Command names are case-insensitive.
- Arguments are split on whitespace.
- A bare prefix routes to `help`.
- Prefix-adjacent words such as `!botping` are ignored so ordinary words are not mistaken for commands.
- Unknown commands receive a friendly reply that suggests `<prefix> help`.

## Ready-to-extend workflow

After the bot responds to built-in commands, add new behavior through the shared command registry used by prefix, slash, mention, and DM paths.

### 1. Create a command class

Create a file such as `../../src/Commands/HelloCommand.php`:

```php
<?php

declare(strict_types=1);

namespace App\Commands;

use App\CommandContext;

final class HelloCommand implements CommandInterface
{
    public function execute(CommandContext $context): string
    {
        return 'Hello from the bot!';
    }

    public function description(): string
    {
        return 'Say hello.';
    }

    public function usage(string $prefix): string
    {
        return CommandUsage::format($prefix, 'hello');
    }
}
```

### 2. Register the command

Edit `../../config/commands.php`:

```php
use App\Commands\HelloCommand;

return [
    // existing commands...
    'hello' => HelloCommand::class,
];
```

Restart the bot process after changing source files.

### 3. Add aliases if useful

Use the registry array form:

```php
'hello' => [
    'class' => HelloCommand::class,
    'aliases' => ['hi'],
],
```

Aliases route to the same command but do not become duplicate full entries in help output.

If the command needs slash-command arguments, use the registry array form with `slash_options`; see [Command registration and aliases](../04-extensibility/command-registration-and-aliases.md).

### 4. Add tests

Use the current offline test style:

- direct command output tests for command behavior;
- router tests for parsing, aliases, and dispatch;
- config validator tests for startup validation changes;
- logger tests for logging behavior changes.

Command tests can instantiate `App\CommandContext` with `discord: null` and `message: null`, which keeps tests independent from Discord network calls.

### 5. Run checks again

```bash
composer lint
composer test
composer check
```

## Operating the running process

Current behavior: this repository starts a long-running PHP CLI process with basic signal handling when `pcntl` is available. It does not include a supervisor, hosted-platform manifest, service unit, health/readiness endpoint, monitoring stack, or Docker setup.

For local development, run `php bin/bot.php` in a terminal. For server use, run the CLI process under an external process manager appropriate for your environment. That process manager remains outside this repository unless future source changes add repository-owned deployment files.

## Troubleshooting checklist

### Missing Composer dependencies

Symptom: `php bin/bot.php` says Composer dependencies are missing.

Fix:

```bash
composer install
```

### Missing token

Symptom: startup fails with a missing `DISCORD_BOT_TOKEN` message.

Fix: set `DISCORD_BOT_TOKEN` in `.env` and restart the bot.

### Invalid prefix

Symptom: startup fails with an invalid `BOT_PREFIX` message.

Fix: use a non-empty prefix without spaces, such as:

```dotenv
BOT_PREFIX=!bot
```

### Invalid timezone

Symptom: startup fails with an invalid `BOT_TIMEZONE` message.

Fix: use a valid PHP timezone identifier, such as:

```dotenv
BOT_TIMEZONE=UTC
```

### Invalid log level

Symptom: startup fails with an invalid `LOG_LEVEL` message.

Fix: use one of:

```dotenv
LOG_LEVEL=debug
LOG_LEVEL=info
LOG_LEVEL=warning
LOG_LEVEL=error
```

### Bot is online but ignores commands

Check all of these:

1. Message Content Intent is enabled in the Discord Developer Portal.
2. The bot process was restarted after enabling the intent.
3. The typed command uses the configured prefix.
4. There is whitespace after the prefix: use `!bot ping`, not `!botping`.
5. The bot has channel permissions to view the channel and send messages.

### Built-in commands work but a new command does not

Check all of these:

1. The command class implements `App\Commands\CommandInterface`.
2. The command namespace is `App\Commands`.
3. The command is registered in `../../config/commands.php`.
4. Composer autoloading can find the class.
5. The long-running bot process was restarted after the change.

## Confidence checklist before extending

Before treating the bot as ready for feature work, confirm:

- `composer install` has completed successfully.
- `.env` contains a real `DISCORD_BOT_TOKEN`.
- Message Content Intent is enabled in Discord.
- The bot is invited to a server and can view/send messages in the test channel.
- `composer lint` passes.
- `composer test` passes.
- `!bot ping` replies `Pong!`.
- `!bot help` lists the current command registry.

When all of those are true, the skeleton is ready to extend through new prefix command classes and tests.
