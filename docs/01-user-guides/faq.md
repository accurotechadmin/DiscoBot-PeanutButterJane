# FAQ

**Audience:** Users with common quick questions.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../README.md`, `../../composer.json`, `../../config/bot.php`, `../../config/commands.php`
**Related docs:** [From GitHub to a running bot](from-github-to-running-bot.md), [Quick start](quick-start.md), [Troubleshooting](troubleshooting.md), [Glossary](../07-reference/glossary.md)

## Will a fresh GitHub checkout run immediately?

Not without setup. Install dependencies, create `.env`, set a real `DISCORD_BOT_TOKEN`, enable Message Content Intent, invite the bot, and start the PHP process. See [From GitHub to a Running Bot](from-github-to-running-bot.md).

## Can I run it with Composer?

Yes. `composer bot` runs `php bin/bot.php`, as declared in `../../composer.json`.

## Does it support slash commands?

Current behavior: yes, when `BOT_ENABLE_SLASH_COMMANDS=true`. Prefix commands remain enabled by default, while slash, mention, and DM paths are opt-in through `.env` settings.

## Does it use Laravel or Symfony?

No. Current behavior: it is framework-free PHP using Composer autoloading and DiscordPHP.

## Does it need a database or queue?

No. Current behavior: built-in commands are stateless and no database or queue code is present.

## Where are logs written?

To the console and, when `LOG_FILE_ENABLED=true`, to daily structured JSON files written by `../../src/ConsoleLogger.php`. Current behavior does not include log aggregation, metrics, or alerting.

## Why are Discord objects nullable in command context?

So command logic can be tested without a live Discord connection. See [Command context reference](../03-technical-reference/command-context-reference.md).

## Where do I add a command?

Create a class under `../../src/Commands/`, implement `CommandInterface`, then register it in `../../config/commands.php`. See [Adding a command](../04-extensibility/adding-a-command.md).
