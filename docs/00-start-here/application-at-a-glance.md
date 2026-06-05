# Application at a Glance

**Audience:** First-time users, command authors, and maintainers.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../bin/bot.php`, `../../config/bot.php`, `../../config/commands.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../src/CommandContext.php`
**Related docs:** [Quick start](../01-user-guides/quick-start.md), [Architecture overview](../03-technical-reference/architecture-overview.md), [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md), [Glossary](../07-reference/glossary.md)

This repository is a compact Discord bot starter. It runs as a PHP command-line process, connects to Discord through DiscordPHP, listens for enabled command paths, and replies to prefix, slash, mention, or direct-message commands according to configuration.

## What happens when it runs

1. `../../bin/bot.php` loads Composer autoloading and optional `.env` values.
2. `../../config/bot.php` provides token, prefix, timezone, environment, and log-level settings.
3. `../../config/commands.php` lists available command classes and aliases.
4. `../../src/ConfigValidator.php` rejects unsafe bot settings, command registry entries, aliases, slash metadata, logging settings, and rate-limit settings before connecting.
5. `../../src/Bot.php` creates the DiscordPHP client with the intents required by enabled interaction paths and installs basic lifecycle signal handling when available.
6. DiscordPHP emits events; the bot listens for `ready`, message events when message-based paths are enabled, and slash interactions when slash commands are enabled.
7. `../../src/Bot.php` ignores bot/self messages.
8. Basic rate limiting runs for command-like interactions, then `../../src/CommandRouter.php` parses or receives the command name and dispatches it.
9. A command class returns a string reply.
10. The reply is sent publicly to a server channel, privately to a DM, or ephemerally to a slash interaction depending on the active path.

## Built-in commands

This orientation table is a short summary; [Command index](../07-reference/command-index.md) is the canonical full command and alias lookup.

| Command | Purpose |
| --- | --- |
| `ping` | Reply with `Pong!` so you can confirm the bot is online. |
| `time` | Show the current time in `BOT_TIMEZONE`. |
| `settings` | Show safe non-secret bot settings. |
| `echo` | Echo provided arguments back to Discord. |
| `help` | List available commands, usage, descriptions, and aliases. |
| `commands` | Alias for `help`. |

## Key vocabulary

- **Bot token:** secret credential from the Discord Developer Portal. Store it in `.env` as `DISCORD_BOT_TOKEN`.
- **Prefix command:** a text command such as `!bot ping` where `!bot` is the prefix and `ping` is the command name.
- **Message Content Intent:** a privileged Discord gateway intent required for reading message text used by prefix, mention, and DM commands.
- **Discord application:** the Developer Portal application that owns the bot user and token.
- **Event loop:** the long-running DiscordPHP process that waits for Discord gateway events.

## Design boundaries

Current behavior: the skeleton is intentionally small and does not include persistence beyond generated daily JSON logs, queues, web controllers, framework service containers, health endpoints, metrics, or monitoring stacks. That keeps the runtime easy to inspect and the command path easy to test.
