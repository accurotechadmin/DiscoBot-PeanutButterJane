# DiscordPHP Integration

**Audience:** Maintainers and technical readers.
**Status:** Current reference
**Last reviewed:** 2026-06-04
**Related files:** `../../src/Bot.php`, `../../bin/bot.php`, `../../composer.json`
**Related docs:** [Runtime lifecycle](runtime-lifecycle.md), [Interaction paths reference](interaction-paths-reference.md), [Inviting the bot](../01-user-guides/inviting-the-bot-to-discord.md)

`../../src/Bot.php` owns the direct DiscordPHP integration.

## Client options

| Option | Current value/purpose |
| --- | --- |
| `token` | From validated bot config. |
| `intents` | Default intents, plus `Intents::MESSAGE_CONTENT` when prefix/mention/DM paths are enabled, plus `Intents::DIRECT_MESSAGES` when DM commands are enabled. |
| `storeMessages` | `false`, keeping the skeleton lightweight. |
| `loadAllMembers` | `false`. |
| `retrieveBans` | `false`. |

## Events and listeners

- `ready`: logs the bot username, registers message handling, and installs slash listeners when slash commands are enabled.
- `MESSAGE_CREATE`: applies the basic user-only command rate limiter and calls the message handler for prefix, mention, and DM paths.
- Slash command listeners: call the slash interaction handler for registered command names and aliases when slash commands are enabled.

## Guards and replies

Messages from bot users and messages authored by the bot itself are ignored before routing.

Prefix and mention replies are sent to the original server channel. DM replies are sent to the direct-message channel. Slash replies use `respondWithMessage(..., true)` so they are ephemeral and visible only to the invoking user.

Slash command definitions are synchronized by `../../bin/sync-slash-commands.php` / `composer sync-slash-commands`, not by normal runtime startup. DiscordPHP owns the event loop after `run()` is called.
