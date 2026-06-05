# Decision Records Index

**Audience:** Maintainers reviewing architectural decisions.
**Status:** Current reference
**Last reviewed:** 2026-06-04
**Related files:** `../../docs/08-architecture-decisions/`, `../../bin/bot.php`, `../../src/CommandRouter.php`, `../../src/CommandContext.php`, `../../src/ConsoleLogger.php`
**Related docs:** [Architecture decisions](../08-architecture-decisions/README.md), [Architecture overview](../03-technical-reference/architecture-overview.md), [Master index](master-index.md)

| ADR | Status | Decision | Main consequence |
| --- | --- | --- | --- |
| [ADR 0001](../08-architecture-decisions/adr-0001-lightweight-cli-discordphp-skeleton.md) | Accepted | Keep a lightweight CLI DiscordPHP skeleton. | Small, inspectable runtime; larger infrastructure absent. |
| [ADR 0002](../08-architecture-decisions/adr-0002-prefix-command-routing.md) | Accepted | Keep prefix command routing as the default. | Simple offline-testable commands; requires Message Content Intent for message content. |
| [ADR 0005](../08-architecture-decisions/adr-0005-configurable-interaction-paths.md) | Accepted | Add configurable prefix, slash, mention, and DM paths. | Preserves prefix defaults while allowing private slash/DM UX and public mention UX. |
| [ADR 0003](../08-architecture-decisions/adr-0003-nullable-discord-objects-for-testability.md) | Accepted | Allow nullable DiscordPHP objects in `CommandContext`. | Commands are easy to test without live Discord objects. |
| [ADR 0004](../08-architecture-decisions/adr-0004-console-only-logging.md) | Accepted | Use lightweight console plus optional daily JSON logging. | No repository-owned log aggregation, metrics, or alerting. |
