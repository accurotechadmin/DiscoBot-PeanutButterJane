# Built-in Commands

**Audience:** Users trying the included commands.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../config/commands.php`, `../../src/Commands/PingCommand.php`, `../../src/Commands/TimeCommand.php`, `../../src/Commands/SettingsCommand.php`, `../../src/Commands/EchoCommand.php`, `../../src/Commands/HelpCommand.php`, `../../tests/BuiltInCommandsTest.php`
**Related docs:** [Interaction paths](interaction-paths.md), [Using prefix commands](using-prefix-commands.md), [Command index](../07-reference/command-index.md), [Command help metadata](../04-extensibility/command-help-metadata.md)

Assuming `BOT_PREFIX=!bot`, these commands are available today through enabled interaction paths. Prefix examples are shown because prefix commands remain enabled by default. For the canonical full command and alias lookup, use [Command index](../07-reference/command-index.md).

| Command | Example | Expected reply shape |
| --- | --- | --- |
| `ping` | `!bot ping` | `Pong!` |
| `time` | `!bot time` | `Current time in <timezone>: <timestamp>` |
| `settings` | `!bot settings` | Multi-line safe summary of prefix, timezone, and environment. |
| `echo` | `!bot echo hello world` | `hello world` |
| `echo` with no text | `!bot echo` | A `Nothing to echo` guidance message with an example. |
| `help` | `!bot help` | Multi-line list of command usage and descriptions. |
| `commands` | `!bot commands` | Same as `help`. |

## Other path examples

When optional paths are enabled, the same commands can also be invoked as `/ping`, `@YourBot ping`, or `ping` in a DM. Slash replies are ephemeral; mention and prefix replies are public in the server; DM replies are private.

## Notes

- `settings` does not print the Discord token.
- `time` uses the configured `BOT_TIMEZONE`.
- `help` uses each command's `usage()` and `description()` methods plus aliases injected by `../../src/CommandRouter.php`.
- Built-in command behavior is covered by `../../tests/BuiltInCommandsTest.php`.
