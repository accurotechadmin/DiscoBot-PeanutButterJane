# Command Index

**Audience:** Users, command authors, and maintainers checking registered commands.
**Status:** Current reference
**Last reviewed:** 2026-06-05
**Related files:** `../../config/commands.php`, `../../src/Commands/PingCommand.php`, `../../src/Commands/TimeCommand.php`, `../../src/Commands/SettingsCommand.php`, `../../src/Commands/EchoCommand.php`, `../../src/Commands/HelpCommand.php`, `../../tests/BuiltInCommandsTest.php`
**Related docs:** [Component inventory](component-inventory.md), [Built-in commands](../01-user-guides/built-in-commands.md), [Interaction paths](../01-user-guides/interaction-paths.md), [Using prefix commands](../01-user-guides/using-prefix-commands.md), [Command registration and aliases](../04-extensibility/command-registration-and-aliases.md)

Current registered commands come from `../../config/commands.php`.

| Typed command | Canonical command | Class | Default prefix usage | Slash/DM usage | Slash options | Reply shape |
| --- | --- | --- | --- | --- | --- | --- |
| `ping` | `ping` | `PingCommand` | `!bot ping` | `/ping` / `ping` | None. | `Pong!` |
| `time` | `time` | `TimeCommand` | `!bot time` | `/time` / `time` | None. | `Current time in <timezone>: <timestamp>` |
| `settings` | `settings` | `SettingsCommand` | `!bot settings` | `/settings` / `settings` | None. | Safe prefix/timezone/environment summary. |
| `echo` | `echo` | `EchoCommand` | `!bot echo <message>` | `/echo <message>` / `echo <message>` | Optional string `arguments`. | Arguments joined by spaces, or usage hint when empty. |
| `help` | `help` | `HelpCommand` | `!bot help` | `/help` / `help` | None. | List commands with descriptions, usage, and aliases. |
| `commands` | `help` | `HelpCommand` | `!bot commands` | `/commands` / `commands` | None. | Alias for `help`; not a duplicate command class. |

## Maintenance note

Documentation examples in `../05-examples/` are not active commands unless their class exists under `../../src/Commands/` and the command is registered in `../../config/commands.php`.
