# Interaction Paths Reference

**Audience:** Maintainers extending or debugging command entry points.
**Status:** Current reference
**Last reviewed:** 2026-06-04
**Related files:** `../../config/bot.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../src/ConfigValidator.php`, `../../src/Commands/CommandUsage.php`, `../../tests/CommandRouterTest.php`, `../../tests/ConfigValidatorTest.php`
**Related docs:** [Runtime lifecycle](runtime-lifecycle.md), [Command routing reference](command-routing-reference.md), [Configuration reference](configuration-reference.md), [ADR 0005](../08-architecture-decisions/adr-0005-configurable-interaction-paths.md)

The runtime supports four independently configurable command entry points: prefix commands, slash commands, mention commands, and direct message commands.

## Config keys

`../../config/bot.php` reads these environment variables into `bot.interactions`:

| Environment variable | Config key | Default | Purpose |
| --- | --- | --- | --- |
| `BOT_ENABLE_PREFIX_COMMANDS` | `prefix_commands` | `true` | Keep the original prefixed text command path. |
| `BOT_ENABLE_SLASH_COMMANDS` | `slash_commands` | `false` | Register and listen for Discord slash commands. |
| `BOT_ENABLE_MENTION_COMMANDS` | `mention_commands` | `false` | Route server messages that start with a bot mention. |
| `BOT_ENABLE_DM_COMMANDS` | `dm_commands` | `false` | Route direct-message content without a prefix. |

`ConfigValidator::validateInteractionToggles()` rejects non-boolean toggle strings during startup validation.

## Gateway event paths

| Path | Discord event | Router method | Reply mechanism | Visibility |
| --- | --- | --- | --- | --- |
| Prefix | `MESSAGE_CREATE` | `route()` / `routeContent()` | `Message::channel->sendMessage()` | Public channel reply. |
| Mention | `MESSAGE_CREATE` | `routeMentionContent()` | `Message::channel->sendMessage()` | Public channel reply. |
| Direct message | `MESSAGE_CREATE` | `routeDirectMessageContent()` | `Message::channel->sendMessage()` | Private DM reply. |
| Slash | Slash interaction listener | `routeCommand()` | `Interaction::respondWithMessage(..., true)` | Ephemeral interaction reply. |

`Bot` checks direct messages before guild message paths. For guild messages, mention routing is attempted before prefix routing so a message that starts with the bot mention is treated as a mention command.

## Intents

`Bot` requests Message Content Intent when any message-content path is enabled: prefix, mention, or DM. It also requests DiscordPHP's direct-message intent when DM commands are enabled.

Slash commands are interaction-based. They do not need the text prefix and are answered with ephemeral interaction responses.

## Slash command synchronization

When slash commands are enabled, normal `Bot` startup installs listeners for command names. It does not register definitions with Discord. Run `composer sync-slash-commands` after enabling slash commands or changing `../../config/commands.php`; that command reads `CommandRouter::slashCommandDefinitions()` and writes definitions to Discord.

The current slash mapping synchronizes configured command names plus aliases. Registered names should be compatible with Discord slash command naming rules. Commands can declare per-command `slash_options` in `../../config/commands.php`; the built-in `echo` command declares an optional string option named `arguments`. Slash option values are split on whitespace and passed into the same command context argument list used by message commands.

## Usage formatting

`App\Commands\CommandUsage` formats help usage for the active path:

| Context prefix | Example output |
| --- | --- |
| `!bot` | `!bot ping` |
| `/` | `/ping` |
| empty string | `ping` |
| `<@123>` | `<@123> ping` |

This keeps help and error hints aligned with the path that invoked the command.
