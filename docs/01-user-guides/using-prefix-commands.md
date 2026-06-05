# Using Prefix Commands

**Audience:** Discord users interacting with the bot.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../config/bot.php`, `../../config/commands.php`, `../../src/CommandRouter.php`
**Related docs:** [Interaction paths](interaction-paths.md), [Built-in commands](built-in-commands.md), [Command routing reference](../03-technical-reference/command-routing-reference.md), [Command index](../07-reference/command-index.md)

Prefix commands are the default interaction path. A prefix command starts with the configured `BOT_PREFIX`, followed by a command name and optional arguments.

With the default prefix:

```text
!bot ping
!bot echo hello world
!bot help
```

For slash, mention, and DM behavior, see [Interaction paths](interaction-paths.md).

## Current prefix parser behavior

| Input | Behavior |
| --- | --- |
| `hello` | Ignored because it does not start with the prefix. |
| `!botping` | Ignored because text is adjacent to the prefix without whitespace. |
| `  !bot ping  ` | Accepted; leading/trailing whitespace is trimmed. |
| `!bot` | Routes to `help`. |
| `!bot PiNg` | Routes to `ping`; command names are case-insensitive. |
| `!bot echo one   two` | Arguments become `one`, `two`; whitespace is collapsed. |
| `!bot unknown` | Replies with a friendly unknown-command message. |
| Empty prefix | Ignored by the router. |

These behaviors are implemented in `../../src/CommandRouter.php` and covered by `../../tests/CommandRouterTest.php`.

## Aliases

Aliases are registered in `../../config/commands.php`. Current behavior: `commands` is an alias for `help`, so `!bot commands` and `!bot help` show the same command list.
