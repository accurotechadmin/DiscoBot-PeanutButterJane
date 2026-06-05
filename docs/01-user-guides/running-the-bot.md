# Running the Bot

**Audience:** Users starting and stopping the CLI process.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../bin/bot.php`, `../../composer.json`, `../../src/Bot.php`
**Related docs:** [From GitHub to a running bot](from-github-to-running-bot.md), [Quick start](quick-start.md), [Running in long-lived sessions](../02-operator-guides/running-in-long-lived-sessions.md), [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md)

## Start commands

```bash
php bin/bot.php
```

or:

```bash
composer bot
```

Both start the same bootstrap in `../../bin/bot.php`; `composer bot` is defined in `../../composer.json`.

## What you should see

The console logger prints timestamped startup messages, including the environment, prefix, timezone, and a debug reminder about Message Content Intent. After DiscordPHP emits `ready`, the bot logs that it is listening for commands.

## Stopping the process

Use your terminal's normal interrupt, usually `Ctrl+C`. The repository does not implement an administrative stop command.

## Long-running use

Current behavior: the repository supplies the CLI application only. If you need restart-on-failure or boot-on-login behavior, use an external process manager appropriate for your environment. See [Running in long-lived sessions](../02-operator-guides/running-in-long-lived-sessions.md).

## Synchronize slash commands

If slash commands are enabled, run `composer sync-slash-commands` after changing `config/commands.php`. Then start the runtime with `composer bot`.
