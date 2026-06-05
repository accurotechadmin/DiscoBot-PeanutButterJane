# Configuration

**Audience:** Users editing `.env` for local or deployed runs.
**Status:** Current guide
**Last reviewed:** 2026-06-05
**Related files:** `../../.env.example`, `../../config/bot.php`, `../../src/ConfigValidator.php`
**Related docs:** [Environment variable index](../07-reference/environment-variable-index.md), [Startup validation](../02-operator-guides/startup-validation.md), [Configuration reference](../03-technical-reference/configuration-reference.md), [Interaction paths](interaction-paths.md)

Configuration is environment-backed. `../../bin/bot.php` loads `.env` if present, then `../../config/bot.php` reads environment variables. For the canonical full lookup table, use [Environment variable index](../07-reference/environment-variable-index.md).

## Variables

| Variable | Must be present in `.env`? | Default from `config/bot.php` | Effective value validation | Notes |
| --- | --- | --- | --- | --- |
| `DISCORD_BOT_TOKEN` | Yes; no usable default exists. | Empty string | Required, no whitespace/control characters, token-like characters. | Passed to DiscordPHP; never print it. |
| `BOT_PREFIX` | No; defaulted when omitted. | `!bot` | Non-empty, no whitespace/control characters, max 32 characters. | Changing it changes what users type. |
| `BOT_TIMEZONE` | No; defaulted when omitted. | `America/Toronto` | Valid PHP timezone identifier. | Used by `time` and shown by `settings`. |
| `APP_ENV` | No; defaulted when omitted. | `local` | `local`, `testing`, `staging`, or `production`. | Displayed by the safe `settings` command. |
| `LOG_LEVEL` | No; defaulted when omitted. | `debug` | One of `debug`, `info`, `warning`, `error`. | Controls console and file log filtering. |
| `LOG_FILE_ENABLED` | No; defaulted when omitted. | `true` | Boolean-like value. | Enables daily structured JSON log files. |
| `LOG_FILE_DIR` | No; defaulted when omitted. | `storage/logs` | Non-empty printable path. | Stores generated `bot-YYYY-MM-DD.json` files. |
| `BOT_ENABLE_PREFIX_COMMANDS` | No; defaulted when omitted. | `true` | Boolean-like value; at least one path must be enabled. | Enables original public prefix commands. |
| `BOT_ENABLE_SLASH_COMMANDS` | No; defaulted when omitted. | `false` | Boolean-like value. | Enables private ephemeral slash command replies. |
| `BOT_ENABLE_MENTION_COMMANDS` | No; defaulted when omitted. | `false` | Boolean-like value. | Enables public bot-mention commands. |
| `BOT_ENABLE_DM_COMMANDS` | No; defaulted when omitted. | `false` | Boolean-like value. | Enables private no-prefix DM commands. |
| `BOT_RATE_LIMIT_MAX_ATTEMPTS` | No; defaulted when omitted. | `5` | Integer `0` or a positive integer; non-integer values are rejected. | `0` disables the basic limiter. |
| `BOT_RATE_LIMIT_WINDOW_SECONDS` | No; defaulted when omitted. | `10` | Positive integer; non-integer values are rejected. | Controls the basic rate-limit window. |

## Example `.env`

```dotenv
DISCORD_BOT_TOKEN=your-token-here
BOT_PREFIX=!bot
BOT_TIMEZONE=America/Toronto
APP_ENV=local
LOG_LEVEL=debug
LOG_FILE_ENABLED=true
LOG_FILE_DIR=storage/logs
BOT_ENABLE_PREFIX_COMMANDS=true
BOT_ENABLE_SLASH_COMMANDS=false
BOT_ENABLE_MENTION_COMMANDS=false
BOT_ENABLE_DM_COMMANDS=false
BOT_RATE_LIMIT_MAX_ATTEMPTS=5
BOT_RATE_LIMIT_WINDOW_SECONDS=10
```

## Validation

`../../src/ConfigValidator.php` validates bot settings, logging settings, interaction toggles, rate-limit settings, and command registry metadata before DiscordPHP connects. This makes many configuration errors fail fast in the terminal instead of appearing as silent Discord behavior.

## Security note

Never commit `.env`. The example file exists to show names and defaults; the real token belongs only in your runtime environment.
