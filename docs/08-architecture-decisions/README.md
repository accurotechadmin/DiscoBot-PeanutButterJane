# Architecture Decisions

**Audience:** Maintainers reviewing why the current design exists.
**Status:** Current reference
**Last reviewed:** 2026-06-04
**Related files:** `../../bin/bot.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../src/CommandContext.php`, `../../src/ConsoleLogger.php`, `../../config/commands.php`
**Related docs:** [Decision records index](../07-reference/decision-records-index.md), [Architecture overview](../03-technical-reference/architecture-overview.md), [Documentation maintenance](../06-maintainer-guides/documentation-maintenance.md)

Architecture decision records (ADRs) document current accepted decisions and tradeoffs. They are intentionally lightweight but should remain useful to a maintainer reading the repository later.

| ADR | Status | Summary | Key source paths |
| --- | --- | --- | --- |
| [ADR 0001](adr-0001-lightweight-cli-discordphp-skeleton.md) | Accepted | Keep the project a lightweight CLI DiscordPHP skeleton. | `../../bin/bot.php`, `../../src/Bot.php` |
| [ADR 0002](adr-0002-prefix-command-routing.md) | Accepted | Keep prefix command routing as the default. | `../../src/CommandRouter.php`, `../../config/commands.php` |
| [ADR 0003](adr-0003-nullable-discord-objects-for-testability.md) | Accepted | Allow command tests without live Discord objects. | `../../src/CommandContext.php`, `../../tests/CommandRouterTest.php` |
| [ADR 0004](adr-0004-console-only-logging.md) | Accepted | Log to console and optional daily structured JSON files without a logging framework. | `../../src/ConsoleLogger.php`, `../../tests/ConsoleLoggerTest.php` |
| [ADR 0005](adr-0005-configurable-interaction-paths.md) | Accepted | Configure prefix, slash, mention, and DM paths independently. | `../../src/Bot.php`, `../../src/CommandRouter.php` |

## When to update an ADR

Update or add an ADR when a change alters architectural boundaries, such as command routing style, runtime model, dependency direction, logging destination, or testability strategy. Small wording fixes do not need new ADRs.
