# Interaction Paths

**Audience:** Users and server operators choosing how people talk to the bot.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../.env.example`, `../../config/bot.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../src/Commands/`
**Related docs:** [Configuration](configuration.md), [Using prefix commands](using-prefix-commands.md), [Interaction paths reference](../03-technical-reference/interaction-paths-reference.md), [Command routing reference](../03-technical-reference/command-routing-reference.md)

The bot can listen on four configurable interaction paths. The owner chooses the enabled paths with `.env` settings.

| Path | Example | Visibility | Prefix text needed from the user | Default |
| --- | --- | --- | --- | --- |
| Prefix command | `!bot ping` | Public in the server channel. | The configured `BOT_PREFIX`. | Enabled. |
| Slash command | `/ping` | Ephemeral response visible only to the user who ran it. | Discord's slash-command UI. | Disabled. |
| Mention command | `@YourBot ping` | Public in the server channel. | A bot mention at the start of the message. | Disabled. |
| Direct message command | `ping` in a DM | Private one-to-one DM between the user and bot. | No prefix, mention, or slash. | Disabled. |

## Environment switches

```dotenv
BOT_ENABLE_PREFIX_COMMANDS=true
BOT_ENABLE_SLASH_COMMANDS=false
BOT_ENABLE_MENTION_COMMANDS=false
BOT_ENABLE_DM_COMMANDS=false
BOT_RATE_LIMIT_MAX_ATTEMPTS=5
BOT_RATE_LIMIT_WINDOW_SECONDS=10
```

Each value accepts `true` or `false`. The startup validator also accepts common boolean forms such as `1`, `0`, `yes`, `no`, `on`, and `off`.

The switches are independent. You may enable one path, all paths, or any combination. To preserve the original skeleton behavior, leave prefix commands enabled and keep the other paths disabled.

## UX differences

### Prefix commands

Prefix commands are normal Discord messages. A user types the configured prefix followed by a command, such as `!bot help`. The original user message and the bot reply are visible in the server channel.

Prefix commands require Message Content Intent because the bot reads message text.

### Slash commands

Slash commands use Discord's application-command UI. The bot registers slash commands for the configured command registry when it becomes ready, then responds to slash interactions.

Slash responses are sent as ephemeral interaction responses, so only the user who ran the command sees the bot reply. Slash commands do not use the text prefix, and the help output formats usage as `/ping`, `/help`, and similar slash forms.

Slash command registration may take time to appear in Discord, especially for global commands. The bot logs a warning if registration fails.

### Mention commands

Mention commands are normal server messages that begin with the bot mention, followed by the command name and arguments:

```text
@YourBot echo hello world
```

The user message and bot reply are public in the channel. A bare bot mention routes to help. Mention commands require Message Content Intent because the bot reads message text.

### Direct message commands

DM commands are private one-to-one messages between the user and bot. In a DM, the user sends only the command name and arguments:

```text
ping
echo hello world
help
```

DM replies stay in the direct message conversation. DM commands require the direct-message gateway intent requested by the bot when the path is enabled.

## Command behavior across paths

The same registered command classes handle all paths. For example, `ping` returns `Pong!` whether it was reached through `!bot ping`, `/ping`, `@YourBot ping`, or `ping` in a DM.

Argument splitting remains whitespace-based for prefix, mention, and DM message content. Slash commands expose the `echo` command with an optional `arguments` text option, then split that text into the existing command argument list.

## Slash command synchronization

After enabling slash commands or changing command registration, run `composer sync-slash-commands` once to synchronize Discord application command definitions. Normal `composer bot` startup only listens for slash interactions; it does not write command definitions to Discord.
