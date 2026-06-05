# Data Flow Index

**Audience:** Maintainers tracing current runtime flows.
**Status:** Current reference
**Last reviewed:** 2026-06-05
**Related files:** `../../bin/bot.php`, `../../bin/sync-slash-commands.php`, `../../config/bot.php`, `../../config/commands.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../src/CommandContext.php`, `../../src/ConsoleLogger.php`, `../../src/RateLimiter.php`, `../../src/RuntimeLifecycle.php`, `../../src/SlashCommandSynchronizer.php`
**Related docs:** [Component inventory](component-inventory.md), [Architecture overview](../03-technical-reference/architecture-overview.md), [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md), [Command routing reference](../03-technical-reference/command-routing-reference.md), [Interaction paths reference](../03-technical-reference/interaction-paths-reference.md)

| Flow | Steps | Detailed doc |
| --- | --- | --- |
| Environment to bot construction | `.env`/process environment -> `config/bot.php` + `config/commands.php` -> `ConfigValidator::validateStartupConfig()` -> `ConsoleLogger`/`CommandRouter`/`RuntimeLifecycle`/`Bot`. | [Configuration reference](../03-technical-reference/configuration-reference.md) |
| Discord message to reply | `MESSAGE_CREATE` -> bot/self guard -> basic user-only rate-limit check for command-like messages -> enabled message path selection -> router parse -> `CommandContext` -> command `execute()` -> non-empty public or DM reply sent. | [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md) |
| Slash interaction to ephemeral reply | slash listener -> basic user-only rate-limit check -> command name/configured options -> `CommandRouter::routeCommand()` -> `CommandContext` -> command `execute()` -> ephemeral interaction response. | [Interaction paths reference](../03-technical-reference/interaction-paths-reference.md) |
| Slash definition synchronization | `composer sync-slash-commands` -> config validation -> `SlashCommandSynchronizer` -> `CommandRouter::slashCommandDefinitions()` -> Discord application command save. | [DiscordPHP integration](../03-technical-reference/discordphp-integration.md) |
| Command registry to help output | `config/commands.php` -> validation -> router command instances -> aliases -> metadata -> `HelpCommand` output. | [Command routing reference](../03-technical-reference/command-routing-reference.md), [Interaction paths reference](../03-technical-reference/interaction-paths-reference.md) |
| Command exception to safe reply | Command throws -> router logs warning -> generic Discord reply. | [Error handling](../03-technical-reference/error-handling.md) |
| Logger filtering and storage | Caller message + level -> normalized severity -> minimum-level check -> console line and optional daily JSON record, or skip. | [Logging reference](../03-technical-reference/logging-reference.md) |
| Runtime signal handling | `Bot::run()` -> `RuntimeLifecycle` installs SIGINT/SIGTERM handlers when `pcntl` exists -> signal requests DiscordPHP close. | [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md) |
