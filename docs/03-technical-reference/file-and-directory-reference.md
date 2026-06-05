# File and Directory Reference

**Audience:** Maintainers looking up repository paths.
**Status:** Current reference
**Last reviewed:** 2026-06-05
**Related files:** `../../README.md`, `../../composer.json`, `../../bin/bot.php`, `../../config/`, `../../src/`, `../../tests/`, `../../storage/logs/.gitkeep`
**Related docs:** [Repository tour](../06-maintainer-guides/repository-tour.md), [Master index](../07-reference/master-index.md), [Component inventory](../07-reference/component-inventory.md), [Architecture overview](architecture-overview.md)

| Path | Role |
| --- | --- |
| `../../README.md` | Project-level quick overview and pointer to the full docs set. |
| `../../docs/` | Maintained documentation set. |
| `../../docs/00-start-here/` | Orientation and documentation navigation. |
| `../../docs/01-user-guides/` | Beginner-friendly setup, full GitHub-to-running-bot path, and usage guides. |
| `../../docs/02-operator-guides/` | Runtime operation, environment, logs, secrets, and dependency guidance. |
| `../../docs/03-technical-reference/` | Source-aligned implementation references. |
| `../../docs/04-extensibility/` | Command-authoring guides. |
| `../../docs/05-examples/` | Copyable documentation examples; not active commands by themselves. |
| `../../docs/06-maintainer-guides/` | Maintenance workflows and checklists. |
| `../../docs/07-reference/` | Dense lookup indexes, component inventory, and glossary. |
| `../../docs/08-architecture-decisions/` | Accepted ADRs for current design choices. |
| `../../bin/bot.php` | CLI bootstrap and event-loop entrypoint. |
| `../../bin/sync-slash-commands.php` | CLI entrypoint for explicit slash command definition synchronization. |
| `../../config/bot.php` | Environment-backed bot settings. |
| `../../config/commands.php` | Command registry and aliases. |
| `../../src/Bot.php` | DiscordPHP integration, enabled interaction listeners, bot/self guards, basic rate-limit checks, lifecycle integration, and reply sending. |
| `../../src/CommandContext.php` | Data object passed into commands. |
| `../../src/CommandRouter.php` | Prefix/mention/DM parsers, slash dispatch, command instantiation, aliases, metadata, and safe dispatch. |
| `../../src/ConfigValidator.php` | Startup validation rules for bot settings, command registry, aliases, slash metadata, interaction toggles, logging, and rate limits. |
| `../../src/ConsoleLogger.php` | LOG_LEVEL-aware console and daily structured JSON logger. |
| `../../src/RateLimiter.php` | Basic in-memory per-key command rate limiter. |
| `../../src/RuntimeLifecycle.php` | Basic signal handling and shutdown logging wrapper. |
| `../../src/SlashCommandSynchronizer.php` | DiscordPHP slash command synchronization helper. |
| `../../src/ParsedCommand.php` | Parsed command name and arguments value object. |
| `../../src/Commands/` | Command interface, command usage helper, and built-in command classes. |
| `../../tests/` | PHPUnit tests for commands, routing, config validation, logger behavior, rate limiting, and lifecycle handling. |
| `../../storage/logs/.gitkeep` | Keeps the default structured log directory in Git; generated JSON logs are ignored. |
