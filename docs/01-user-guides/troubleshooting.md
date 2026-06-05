# Troubleshooting

**Audience:** Users diagnosing startup or command issues.
**Status:** Current guide
**Last reviewed:** 2026-06-03
**Related files:** `../../bin/bot.php`, `../../src/ConfigValidator.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../src/ConsoleLogger.php`
**Related docs:** [Configuration](configuration.md), [Startup validation](../02-operator-guides/startup-validation.md), [Error handling](../03-technical-reference/error-handling.md)

## Startup problems

| Symptom | Likely cause | What to check |
| --- | --- | --- |
| Missing Composer dependencies | `vendor/autoload.php` is absent | Run `composer install`. |
| `Missing DISCORD_BOT_TOKEN` | Token is blank or `.env` was not loaded | Check `.env` and runtime environment. |
| `Invalid BOT_PREFIX` | Prefix is empty or contains whitespace | Use a short value such as `!bot`. |
| `Invalid BOT_TIMEZONE` | Timezone is not a PHP identifier | Try `UTC` or `America/Toronto`. |
| `Invalid LOG_LEVEL` | Unsupported log level | Use `debug`, `info`, `warning`, or `error`. |

## Bot connects but does not reply

- Confirm the message starts with the exact configured prefix.
- Avoid prefix-adjacent text: `!bot ping` works, `!botping` is ignored.
- Confirm Message Content Intent is enabled in the Discord Developer Portal.
- Confirm the bot has permission to view the channel and send messages.
- Check console output and daily JSON logs when `LOG_FILE_ENABLED=true`.

## Command fails with generic reply

If a command throws an exception, `../../src/CommandRouter.php` logs implementation details at warning level and returns `Sorry, something went wrong while running that command.` to Discord. This prevents sensitive exception details from being sent to users.

## Not implemented here

Current behavior: generated daily JSON logs are written under `storage/logs` when `LOG_FILE_ENABLED=true`; `storage/logs/.gitkeep` keeps that directory present. Use external aggregation/retention if your runtime needs more than the local JSON trail.
