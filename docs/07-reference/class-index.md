# Class Index

**Audience:** Maintainers looking up PHP classes.
**Status:** Current reference
**Last reviewed:** 2026-06-05
**Related files:** `../../src/Bot.php`, `../../src/CommandContext.php`, `../../src/CommandRouter.php`, `../../src/ConfigValidator.php`, `../../src/ConsoleLogger.php`, `../../src/RateLimiter.php`, `../../src/RuntimeLifecycle.php`, `../../src/SlashCommandSynchronizer.php`, `../../src/ParsedCommand.php`, `../../src/Commands/`
**Related docs:** [Component inventory](component-inventory.md), [Architecture overview](../03-technical-reference/architecture-overview.md), [Command context reference](../03-technical-reference/command-context-reference.md), [Command routing reference](../03-technical-reference/command-routing-reference.md)

| Class/interface | Path | Responsibility | Key docs |
| --- | --- | --- | --- |
| `App\Bot` | `../../src/Bot.php` | Create DiscordPHP client, listen for enabled message/slash events, ignore bot/self messages, apply basic rate limiting, send public, private, and ephemeral replies. | [DiscordPHP integration](../03-technical-reference/discordphp-integration.md) |
| `App\CommandContext` | `../../src/CommandContext.php` | Carry Discord objects, command name, arguments, prefix, and config into commands. | [Command context reference](../03-technical-reference/command-context-reference.md) |
| `App\CommandRouter` | `../../src/CommandRouter.php` | Register commands, parse prefix/mention/DM messages, dispatch slash command names, resolve aliases, inject metadata, catch command exceptions. | [Command routing reference](../03-technical-reference/command-routing-reference.md) |
| `App\ConfigValidator` | `../../src/ConfigValidator.php` | Validate bot settings, interaction toggles, rate limits, command registry, aliases, and slash metadata before startup. | [Configuration reference](../03-technical-reference/configuration-reference.md) |
| `App\ConsoleLogger` | `../../src/ConsoleLogger.php` | Write LOG_LEVEL-filtered console lines and optional daily structured JSON records. | [Logging reference](../03-technical-reference/logging-reference.md) |
| `App\RateLimiter` | `../../src/RateLimiter.php` | Enforce the basic in-memory per-key command rate limit. | [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md) |
| `App\RuntimeLifecycle` | `../../src/RuntimeLifecycle.php` | Install basic SIGINT/SIGTERM handlers and shutdown logging when `pcntl` is available. | [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md) |
| `App\SlashCommandSynchronizer` | `../../src/SlashCommandSynchronizer.php` | Synchronize slash command definitions outside normal bot runtime startup. | [DiscordPHP integration](../03-technical-reference/discordphp-integration.md) |
| `App\ParsedCommand` | `../../src/ParsedCommand.php` | Store parsed command name and argument list. | [Command routing reference](../03-technical-reference/command-routing-reference.md) |
| `App\Commands\CommandInterface` | `../../src/Commands/CommandInterface.php` | Define command method contract. | [Command interface contract](../04-extensibility/command-interface-contract.md) |
| `App\Commands\CommandUsage` | `../../src/Commands/CommandUsage.php` | Format usage strings for prefix, slash, mention, and DM paths. | [Interaction paths reference](../03-technical-reference/interaction-paths-reference.md) |
| `App\Commands\PingCommand` | `../../src/Commands/PingCommand.php` | Return `Pong!`. | [Command index](command-index.md) |
| `App\Commands\TimeCommand` | `../../src/Commands/TimeCommand.php` | Return current time in configured timezone. | [Built-in commands](../01-user-guides/built-in-commands.md) |
| `App\Commands\SettingsCommand` | `../../src/Commands/SettingsCommand.php` | Return safe prefix/timezone/environment summary. | [Built-in commands](../01-user-guides/built-in-commands.md) |
| `App\Commands\EchoCommand` | `../../src/Commands/EchoCommand.php` | Echo arguments or show usage hint. | [Built-in commands](../01-user-guides/built-in-commands.md) |
| `App\Commands\HelpCommand` | `../../src/Commands/HelpCommand.php` | Format command metadata and aliases. | [Command help metadata](../04-extensibility/command-help-metadata.md) |
