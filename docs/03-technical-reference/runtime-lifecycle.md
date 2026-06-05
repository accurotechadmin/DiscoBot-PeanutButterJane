# Runtime Lifecycle

**Audience:** Maintainers tracing startup and message handling.
**Status:** Current reference
**Last reviewed:** 2026-06-04
**Related files:** `../../bin/bot.php`, `../../config/bot.php`, `../../config/commands.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../src/ConfigValidator.php`, `../../src/ConsoleLogger.php`, `../../src/RateLimiter.php`, `../../src/RuntimeLifecycle.php`, `../../src/SlashCommandSynchronizer.php`
**Related docs:** [Architecture overview](architecture-overview.md), [DiscordPHP integration](discordphp-integration.md), [Command routing reference](command-routing-reference.md), [Error handling](error-handling.md)

This page traces the current runtime from terminal command to Discord reply.

## Bootstrap lifecycle

1. Operator runs `php bin/bot.php` or `composer bot`.
2. `../../bin/bot.php` checks for `vendor/autoload.php` and exits with a clear STDERR message if Composer dependencies are missing.
3. Composer autoloading is required.
4. A local `.env` file is loaded if it exists and is readable; existing process variables are not overwritten.
5. `../../config/bot.php` and `../../config/commands.php` are required into a config array.
6. `ConfigValidator::validateStartupConfig()` validates bot settings, command registry shape, aliases, and slash option metadata.
7. `ConsoleLogger` is created with the configured `LOG_LEVEL` and optional daily structured JSON file output.
8. `CommandRouter` is created from the command registry.
9. `RuntimeLifecycle` and `Bot` are created with config, router, logger, and the basic rate limiter.
10. `Bot::run()` installs available signal handlers and starts DiscordPHP's long-running event loop.

## Discord client lifecycle

`../../src/Bot.php` constructs `Discord\Discord` with the configured token, DiscordPHP default intents, and memory-conscious options: `storeMessages => false`, `loadAllMembers => false`, and `retrieveBans => false`. It adds `Intents::MESSAGE_CONTENT` only when at least one message-content path is enabled: prefix, mention, or DM commands. It adds `Intents::DIRECT_MESSAGES` when DM commands are enabled.

The bot listens for `ready`. Inside that handler, it subscribes to `Event::MESSAGE_CREATE` only when at least one message-based path is enabled. When slash commands are enabled, the ready handler installs slash interaction listeners for command names that were synchronized separately with `composer sync-slash-commands`. Normal runtime startup does not write slash command definitions to Discord.

## Message lifecycle

1. DiscordPHP emits `MESSAGE_CREATE` with a message and Discord client.
2. For message events, `Bot` ignores messages authored by bots.
3. `Bot` also ignores messages authored by the bot user itself.
4. `Bot` applies the basic per-user rate limiter when the message could invoke an enabled command path; message command limits follow the user across channels.
5. `Bot` selects the enabled message path: DM commands, mention commands, or prefix commands.
6. The router parses the selected message content, resolves aliases, and builds `CommandContext`.
7. The matched command returns a string.
8. Empty or `null` replies are not sent.
9. Non-empty prefix and mention replies are sent publicly to the originating server channel; non-empty DM replies are sent to the one-to-one DM channel.
10. Send failures are caught and logged as warnings.

## Slash interaction lifecycle

1. Slash commands are available only when `BOT_ENABLE_SLASH_COMMANDS=true`.
2. Slash command definitions are synchronized outside normal runtime with `composer sync-slash-commands`.
3. After DiscordPHP emits `ready`, `Bot` installs slash listeners for those names.
4. Discord invokes the listener with the slash command name and option values.
5. `Bot` collects option values into the same argument list shape used by message commands.
6. `CommandRouter::routeCommand()` resolves aliases, builds `CommandContext` with `/` as the usage prefix, and dispatches the command.
7. Non-empty slash replies are sent with `respondWithMessage(..., true)`, which makes them ephemeral interaction responses.

## Shutdown behavior

Current behavior: `RuntimeLifecycle` installs SIGINT and SIGTERM handlers when the `pcntl` extension is available. The first received signal marks shutdown as requested, invokes the Discord shutdown callback, requests DiscordPHP shutdown through `close()` when DiscordPHP exposes it, and logs process shutdown. Repeated shutdown requests are ignored so the close callback runs once. Reconnect/backoff, health checks, readiness endpoints, and external process supervision are not implemented in this repository and remain **Future consideration**.
