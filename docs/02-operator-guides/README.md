# Operator Guides

**Audience:** People responsible for running the bot process.
**Status:** Current guide
**Last reviewed:** 2026-06-05
**Related files:** `../../bin/bot.php`, `../../.env.example`, `../../config/bot.php`, `../../src/ConsoleLogger.php`, `../../composer.json`
**Related docs:** [Authoritative installation, startup, and configuration guide](../01-user-guides/installation-startup-configuration-guide.md), [Environment management](environment-management.md), [Security and secrets](security-and-secrets.md), [Startup validation](startup-validation.md), [Logging and log levels](logging-and-log-levels.md), [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md)

These guides focus on operating the current CLI process safely. The repository provides a lightweight DiscordPHP bot process, not a deployment platform or infrastructure bundle.

## Operator page map

| Page | Use it for | Source alignment |
| --- | --- | --- |
| [Authoritative installation, startup, and configuration guide](../01-user-guides/installation-startup-configuration-guide.md) | Follow the consolidated local-to-production-server installation, startup, configuration, and operation path. | `../../README.md`, `../../.env.example`, `../../composer.json`, `../../bin/bot.php`, `../../config/bot.php` |
| [Environment management](environment-management.md) | Keep `.env` and process environment values predictable. | `../../bin/bot.php`, `../../config/bot.php`, `../../.env.example` |
| [Security and secrets](security-and-secrets.md) | Protect the Discord bot token and avoid leaking runtime settings. | `../../.env.example`, `../../config/bot.php`, `../../src/Commands/SettingsCommand.php` |
| [Startup validation](startup-validation.md) | Understand fail-fast checks before DiscordPHP connects. | `../../bin/bot.php`, `../../src/ConfigValidator.php` |
| [Logging and log levels](logging-and-log-levels.md) | Interpret console output and choose `LOG_LEVEL`. | `../../src/ConsoleLogger.php`, `../../tests/ConsoleLoggerTest.php` |
| [Dependency management](dependency-management.md) | Install and check Composer dependencies. | `../../composer.json` |
| [Running in long-lived sessions](running-in-long-lived-sessions.md) | Keep the CLI process online with external supervision. | `../../bin/bot.php`, `../../src/Bot.php` |

## Operational boundaries

Current behavior: this repository does not include a systemd unit, Docker file, hosted-platform manifest, external monitoring integration, queue worker, or log aggregation pipeline. If you use any of those tools, treat them as external operational options around `php bin/bot.php` or `composer bot`, not as features implemented by this codebase.

**Future consideration:** repository-owned deployment manifests or monitoring integrations should be added only with matching source files, tests or verification guidance, and documentation updates.

## Routine operator checklist

- Install dependencies with `composer install` before starting the process.
- Configure exactly one effective source for `DISCORD_BOT_TOKEN`, `BOT_PREFIX`, `BOT_TIMEZONE`, `APP_ENV`, and `LOG_LEVEL`.
- Verify Message Content Intent is enabled in the Discord Developer Portal before investigating command parsing.
- Start the bot with `php bin/bot.php` or `composer bot`.
- Watch STDOUT/STDERR for startup validation failures and DiscordPHP connection messages.
- Use the generated daily JSON logs for a basic local trail; provide external aggregation/retention if your environment requires it.
