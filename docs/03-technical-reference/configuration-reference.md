# Configuration Reference

**Audience:** Maintainers and operators verifying runtime settings.
**Status:** Current reference
**Last reviewed:** 2026-06-05
**Related files:** `../../.env.example`, `../../config/bot.php`, `../../config/commands.php`, `../../src/ConfigValidator.php`, `../../tests/ConfigValidatorTest.php`
**Related docs:** [Environment management](../02-operator-guides/environment-management.md), [Startup validation](../02-operator-guides/startup-validation.md), [Environment variable index](../07-reference/environment-variable-index.md)

`../../config/bot.php` reads process environment values and returns the runtime bot config. `../../bin/bot.php` validates that config together with `../../config/commands.php` before constructing DiscordPHP.

| Config key | Environment variable | Default | Validation |
| --- | --- | --- | --- |
| `token` | `DISCORD_BOT_TOKEN` | blank | Required, no whitespace/control characters, token-like characters. |
| `prefix` | `BOT_PREFIX` | `!bot` | Non-empty, no whitespace, no control characters, max 32 characters. |
| `timezone` | `BOT_TIMEZONE` | `America/Toronto` | PHP timezone identifier. |
| `env` | `APP_ENV` | `local` | `local`, `testing`, `staging`, or `production`. |
| `log_level` | `LOG_LEVEL` | `debug` | `debug`, `info`, `warning`, or `error`. |
| `logging.file_enabled` | `LOG_FILE_ENABLED` | `true` | Boolean-like value. |
| `logging.directory` | `LOG_FILE_DIR` | `storage/logs` | Non-empty printable path. |
| `interactions.prefix_commands` | `BOT_ENABLE_PREFIX_COMMANDS` | `true` | Boolean-like value; at least one interaction path must be enabled. |
| `interactions.slash_commands` | `BOT_ENABLE_SLASH_COMMANDS` | `false` | Boolean-like value. |
| `interactions.mention_commands` | `BOT_ENABLE_MENTION_COMMANDS` | `false` | Boolean-like value. |
| `interactions.dm_commands` | `BOT_ENABLE_DM_COMMANDS` | `false` | Boolean-like value. |
| `rate_limit.max_attempts` | `BOT_RATE_LIMIT_MAX_ATTEMPTS` | `5` | Integer `0` or a positive integer; non-integer values are rejected. `0` disables the basic limiter. |
| `rate_limit.window_seconds` | `BOT_RATE_LIMIT_WINDOW_SECONDS` | `10` | Positive integer; non-integer values are rejected. |

`ConfigValidator::validateCommandRegistry()` also validates command names, aliases, duplicate definitions, command classes, slash option names, option descriptions, option types, and duplicate slash options before startup.
