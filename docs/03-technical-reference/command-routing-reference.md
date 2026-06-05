# Command Routing Reference

**Audience:** Maintainers verifying parser, alias, dispatch, and command metadata behavior.
**Status:** Current reference
**Last reviewed:** 2026-06-04
**Related files:** `../../src/CommandRouter.php`, `../../src/ParsedCommand.php`, `../../src/CommandContext.php`, `../../config/commands.php`, `../../tests/CommandRouterTest.php`
**Related docs:** [Interaction paths reference](interaction-paths-reference.md), [Using prefix commands](../01-user-guides/using-prefix-commands.md), [Command registration and aliases](../04-extensibility/command-registration-and-aliases.md), [ADR 0002](../08-architecture-decisions/adr-0002-prefix-command-routing.md), [ADR 0005](../08-architecture-decisions/adr-0005-configurable-interaction-paths.md)

`App\CommandRouter` owns command registration, alias resolution, prefix/mention/DM parsing, slash dispatch, slash option metadata, help metadata injection, and safe command dispatch. `../../tests/CommandRouterTest.php` is the executable source of truth for parser edge cases.

## Production and test entrypoints

| Method | Input | DiscordPHP objects | Use |
| --- | --- | --- | --- |
| `route()` | `Discord`, `Message`, prefix, config | Required | Production prefix path from `App\Bot`. |
| `routeContent()` | Raw message content, prefix, config | Nullable optional Discord objects | Offline prefix parser/command checks. |
| `routeMentionContent()` | Raw message content, bot user ID, config | Nullable optional Discord objects | Mention commands that start with `<@bot-id>` or `<@!bot-id>`. |
| `routeDirectMessageContent()` | Raw DM content, config | Nullable optional Discord objects | DM commands without a prefix. |
| `routeCommand()` | Command name, argument list, usage prefix, config | Nullable optional Discord objects | Shared dispatch for slash commands and parsed message commands. |
| `parse()` | Raw message content and prefix | Not used | Parser-only behavior for prefix commands. |

## Prefix parser behavior

| Input condition | Result |
| --- | --- |
| Empty prefix | Ignore content and return `null`. |
| Leading/trailing whitespace around content | Trimmed before prefix checks. |
| No configured prefix at the start | Ignored with `null`. |
| Prefix-adjacent text such as `!botping` | Ignored with `null`; it is not `!bot ping`. |
| Valid command with extra leading/trailing whitespace | Accepted. |
| Multiple spaces or tabs between command and arguments | Collapsed by whitespace splitting. |
| Bare prefix such as `!bot` | Routes to `help`. |
| Command names | Trimmed and lowercased. |
| Arguments | Split on whitespace into `list<string>`. |

## Mention and DM parser behavior

Mention commands must start with the bot mention and then whitespace or the end of the message. A bare mention routes to `help`; mention-adjacent text such as `<@123>ping` is ignored.

DM commands parse the first whitespace-delimited token as the command name and the remaining tokens as arguments. Empty DM content is ignored.

## Alias, unknown-command, and exception behavior

Aliases are resolved before command lookup. With the current registry, `commands` points to `help` and help output displays it as an alias instead of a duplicate command.

Unknown commands return a friendly message whose help hint matches the active path, such as `!bot help`, `/help`, `<@123> help`, or `help` in a DM.

Command exceptions are caught, logged with implementation details for maintainers, and converted to the safe Discord reply `Sorry, something went wrong while running that command.`

## Registry to command flow

```text
config/commands.php
    -> CommandRouter::__construct()
    -> register each command name
    -> instantiate class or accept CommandInterface instance
    -> register aliases to canonical normalized command names
    -> metadata(active usage prefix) reads description(), usage(), and aliases
    -> HelpCommand reads commands.metadata from context config
```

## Slash command definitions

`slashCommandDefinitions()` returns every canonical command and alias with a Discord-safe description and an `options` list. The registry can provide `slash_options` per command; aliases inherit their canonical command's options. This supports newly added commands that need slash arguments without changing `CommandInterface`.

## Message and slash flows

```text
Bot receives MESSAGE_CREATE
    -> ignore bot/self messages in Bot
    -> choose DM, mention, or prefix path based on config and message shape
    -> CommandRouter parses message text
    -> routeCommand() resolves aliases and builds CommandContext
    -> CommandInterface::execute()
    -> Bot sends non-empty string replies to the original channel or DM
```

```text
Bot receives slash interaction callback
    -> slash listener extracts command name and option text
    -> routeCommand() resolves aliases and builds CommandContext with `/` usage prefix
    -> CommandInterface::execute()
    -> Bot sends an ephemeral interaction response
```
