# Running in Long-Lived Sessions

**Audience:** Operators keeping the CLI bot online.
**Status:** Current guide
**Last reviewed:** 2026-06-03
**Related files:** `../../bin/bot.php`, `../../composer.json`, `../../src/Bot.php`, `../../src/ConsoleLogger.php`
**Related docs:** [Running the bot](../01-user-guides/running-the-bot.md), [Logging and log levels](logging-and-log-levels.md), [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md), [Error handling](../03-technical-reference/error-handling.md)

The bot is a long-running CLI process. Start it with `php bin/bot.php` or the Composer script `composer bot`; both paths run the same bootstrap in `../../bin/bot.php`.

## Current repository boundary

Current behavior: this repository does not include a process-manager configuration, container image, hosted-platform manifest, service unit, or restart policy. DiscordPHP owns the event loop after `App\Bot::run()` calls the Discord client.

External process managers can still supervise the command, but they are outside this repository. Configure them to run from the project root, provide the expected environment variables, and capture STDOUT/STDERR.

## Generic process-manager expectations

When wrapping the bot with external tooling, make sure the wrapper can:

1. Run `composer install` or otherwise provide `vendor/autoload.php` before startup.
2. Set or mount `DISCORD_BOT_TOKEN` securely.
3. Preserve the desired working directory so relative paths in `bin/bot.php` resolve correctly.
4. Restart only after configuration failures are fixed; repeated restarts will not repair an invalid token, prefix, timezone, or log level.
5. Capture console output if operational history is needed.
6. Stop the PHP process gracefully when deploying updates.

## Failure modes to recognize

| Symptom | Likely cause | First check |
| --- | --- | --- |
| Immediate STDERR error about Composer dependencies | `vendor/autoload.php` is missing. | Run `composer install`. |
| Immediate STDERR error about `DISCORD_BOT_TOKEN` | Token is blank or unavailable to the process. | Verify `.env` or injected environment. |
| Immediate STDERR error about prefix/timezone/log level | Startup validation rejected config. | See [Startup validation](startup-validation.md). |
| Process starts but commands do not reply | Discord intent, invite permissions, prefix, or channel access issue. | See [Troubleshooting](../01-user-guides/troubleshooting.md). |
| Replies sometimes fail | Discord channel send failed and was logged as a warning. | Inspect console warning output. |

## Restart checklist

- Pull or deploy documentation/code changes.
- Run `composer install` if dependencies changed.
- Run `composer check` when development dependencies are available.
- Confirm `.env` or externally provided variables are correct.
- Restart the long-running PHP command.
- Send `!bot ping` or the configured-prefix equivalent in an allowed channel.

## Shutdown behavior

When the `pcntl` extension is available, the runtime installs basic SIGINT and SIGTERM handlers and asks DiscordPHP to close before process shutdown. External process supervision, health checks, readiness endpoints, reconnect orchestration, and monitoring remain outside this repository. **Future consideration:** add those only with source changes, tests, and operator guidance.
