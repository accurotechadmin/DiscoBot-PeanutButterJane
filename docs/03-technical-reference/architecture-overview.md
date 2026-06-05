# Architecture Overview

**Audience:** Maintainers needing the current runtime shape.
**Status:** Current reference
**Last reviewed:** 2026-06-05
**Related files:** `../../bin/bot.php`, `../../config/bot.php`, `../../config/commands.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../src/CommandContext.php`, `../../src/ConsoleLogger.php`
**Related docs:** [Runtime lifecycle](runtime-lifecycle.md), [Command routing reference](command-routing-reference.md), [Component inventory](../07-reference/component-inventory.md), [ADR 0001](../08-architecture-decisions/adr-0001-lightweight-cli-discordphp-skeleton.md), [ADR 0002](../08-architecture-decisions/adr-0002-prefix-command-routing.md)

The application is a small PHP CLI Discord bot. DiscordPHP provides the gateway client and event loop; this repository provides startup wiring, configuration validation, command parsing, command classes, and tests.

## Current architecture

```text
.env / process env
    -> config/bot.php + config/commands.php
    -> ConfigValidator
    -> ConsoleLogger + CommandRouter + RuntimeLifecycle + RateLimiter + Bot
    -> DiscordPHP gateway event loop
    -> MESSAGE_CREATE or slash interaction
    -> bot/self guard for message events
    -> basic command rate limit
    -> CommandRouter parse/alias/dispatch
    -> CommandContext
    -> CommandInterface::execute()
    -> string reply
```

## Component responsibilities

| Component | Responsibility | Not responsible for |
| --- | --- | --- |
| `../../bin/bot.php` | Composer autoload check, optional `.env` loading, config assembly, validation, logger/router/bot construction. | Discord event handling after `App\Bot::run()`. |
| `../../config/bot.php` | Environment-backed bot settings. | Validation rules; those live in `ConfigValidator`. |
| `../../config/commands.php` | Command registry and aliases. | Command execution logic. |
| `../../src/ConfigValidator.php` | Startup validation for bot config, logging/rate-limit settings, command registry, aliases, and slash metadata. | Runtime Discord API checks. |
| `../../src/ConsoleLogger.php` | Timestamped console output and optional daily structured JSON log files with severity filtering. | Log aggregation, metrics, or alerting. |
| `../../src/Bot.php` | DiscordPHP client creation, enabled interaction listeners, bot/self guards, basic rate-limit checks, public message replies, and ephemeral slash replies. | Command execution details or slash definition synchronization. |
| `../../src/CommandRouter.php` | Command instantiation, alias map, registry metadata validation, prefix/mention/DM parsing, slash dispatch, metadata injection, safe command exception handling. | Sending Discord messages or interaction responses. |
| `../../src/CommandContext.php` | Data object passed into commands. | Mutating Discord state by itself. |
| `../../src/RateLimiter.php` | Basic in-memory per-key command rate limiting. | Distributed or persistent quota storage. |
| `../../src/RuntimeLifecycle.php` | Basic SIGINT/SIGTERM handling when `pcntl` is available. | Process supervision, health checks, or reconnect orchestration. |
| `../../src/SlashCommandSynchronizer.php` | Explicit slash command definition synchronization. | Normal message/slash event handling. |
| `../../src/Commands/*` | Built-in command replies and help metadata. | Registration outside `config/commands.php`. |

## Explicit boundaries

Current behavior: there is no framework container, database layer, queue worker, controller layer, health/readiness endpoint, metrics backend, or monitoring stack. See [ADR 0001](../08-architecture-decisions/adr-0001-lightweight-cli-discordphp-skeleton.md) for the lightweight CLI decision and [ADR 0002](../08-architecture-decisions/adr-0002-prefix-command-routing.md) for prefix routing.

**Future consideration:** adding any larger infrastructure should include code, tests or verification guidance, a new or updated ADR, and updates to user/operator docs.
