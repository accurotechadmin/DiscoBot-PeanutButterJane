# Technical Reference

**Audience:** Maintainers and contributors who need source-aligned implementation details.
**Status:** Current reference
**Last reviewed:** 2026-06-05
**Related files:** `../../bin/bot.php`, `../../config/bot.php`, `../../config/commands.php`, `../../src/`, `../../tests/`, `../../composer.json`
**Related docs:** [Architecture overview](architecture-overview.md), [Runtime lifecycle](runtime-lifecycle.md), [Command routing reference](command-routing-reference.md), [Configuration reference](configuration-reference.md), [Component inventory](../07-reference/component-inventory.md), [Reference index](../07-reference/README.md)

Use this section when a user guide intentionally stays high-level. These pages describe the current source behavior and should be updated whenever matching code or tests change.

## Reference map

| Page | Primary question | Key source paths |
| --- | --- | --- |
| [Architecture overview](architecture-overview.md) | What are the major runtime parts? | `../../bin/bot.php`, `../../src/Bot.php`, `../../src/CommandRouter.php` |
| [Component inventory](../07-reference/component-inventory.md) | How do implemented components map to source, tests, docs, and connected pieces? | `../../bin/`, `../../config/`, `../../src/`, `../../tests/` |
| [Runtime lifecycle](runtime-lifecycle.md) | What happens from CLI start to reply? | `../../bin/bot.php`, `../../src/Bot.php` |
| [Configuration reference](configuration-reference.md) | How do environment variables become config? | `../../.env.example`, `../../config/bot.php`, `../../src/ConfigValidator.php` |
| [Command routing reference](command-routing-reference.md) | How exactly does parsing and dispatch work? | `../../src/CommandRouter.php`, `../../tests/CommandRouterTest.php` |
| [Interaction paths reference](interaction-paths-reference.md) | How do prefix, slash, mention, and DM entry points differ? | `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../config/bot.php` |
| [Command context reference](command-context-reference.md) | What does a command receive? | `../../src/CommandContext.php` |
| [DiscordPHP integration](discordphp-integration.md) | How is the Discord client configured? | `../../src/Bot.php` |
| [Error handling](error-handling.md) | Which failures are caught and where? | `../../bin/bot.php`, `../../src/Bot.php`, `../../src/CommandRouter.php` |
| [Logging reference](logging-reference.md) | What does the logger do? | `../../src/ConsoleLogger.php`, `../../tests/ConsoleLoggerTest.php` |
| [Testing reference](testing-reference.md) | Which tests cover current behavior? | `../../tests/` |
| [Composer scripts reference](composer-scripts-reference.md) | What project scripts are available? | `../../composer.json` |
| [File and directory reference](file-and-directory-reference.md) | What does each path do? | Repository root |

## Maintenance rule

Do not update these pages from memory. Verify the relevant source path first, then update the reference and any cross-linked user/operator/example pages that depend on the same behavior.
