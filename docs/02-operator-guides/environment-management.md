# Environment Management

**Audience:** Operators managing runtime settings.
**Status:** Current guide
**Last reviewed:** 2026-06-05
**Related files:** `../../.env.example`, `../../bin/bot.php`, `../../bin/sync-slash-commands.php`, `../../config/bot.php`, `../../src/ConfigValidator.php`
**Related docs:** [Configuration](../01-user-guides/configuration.md), [Environment variable index](../07-reference/environment-variable-index.md), [Configuration reference](../03-technical-reference/configuration-reference.md), [Startup validation](startup-validation.md)

`../../bin/bot.php` and `../../bin/sync-slash-commands.php` load `.env` when it exists and is readable. They do not overwrite variables already present in the process environment, which allows local `.env` development while still supporting externally provided environment variables. For the canonical full variable table, use [Environment variable index](../07-reference/environment-variable-index.md).

## Current variables

| Variable | Operational note | Default from source |
| --- | --- | --- |
| `DISCORD_BOT_TOKEN` | Secret; required and token-like. | Blank in `.env.example`; no usable default. |
| `BOT_PREFIX` | Changing it changes what users type. Whitespace/control characters are rejected. | `!bot` |
| `BOT_TIMEZONE` | Affects `time` command output. | `America/Toronto` |
| `APP_ENV` | Displayed by `settings`; use `local`, `testing`, `staging`, or `production`. | `local` |
| `LOG_LEVEL` | Controls minimum console and structured-file log severity. | `debug` |
| `LOG_FILE_ENABLED` | Enables or disables generated daily JSON log files. | `true` |
| `LOG_FILE_DIR` | Directory for generated `bot-YYYY-MM-DD.json` files. | `storage/logs` |
| `BOT_ENABLE_PREFIX_COMMANDS` | Enables original public prefix command interaction path. | `true` |
| `BOT_ENABLE_SLASH_COMMANDS` | Enables slash command listeners and private ephemeral replies; synchronize definitions with `composer sync-slash-commands`. | `false` |
| `BOT_ENABLE_MENTION_COMMANDS` | Enables public bot-mention command interaction path. | `false` |
| `BOT_ENABLE_DM_COMMANDS` | Enables private direct-message commands without a prefix. | `false` |
| `BOT_RATE_LIMIT_MAX_ATTEMPTS` | Integer maximum commands per rate-limit window; `0` disables the basic limiter; non-integer values fail startup validation. | `5` |
| `BOT_RATE_LIMIT_WINDOW_SECONDS` | Positive integer basic rate-limit window size; non-integer values fail startup validation. | `10` |

## Precedence model

1. Values already present in the process environment win.
2. Missing values can be loaded from `.env`.
3. `../../config/bot.php` applies defaults for optional values.
4. `../../src/ConfigValidator.php` rejects invalid required/effective values before Discord connects.

## Checklist

- Keep one authoritative token source per runtime.
- Document prefix and interaction-path changes for users before deployment.
- Run `composer sync-slash-commands` after slash command registry changes.
- Validate timezone names with PHP identifiers such as `UTC` or `America/Toronto`.
- Prefer externally injected secrets for shared environments; use `.env` for local development.
- Restart the long-running process after changing environment variables.
