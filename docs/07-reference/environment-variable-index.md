# Environment Variable Index

**Audience:** Users, operators, and maintainers checking supported runtime variables.
**Status:** Current reference
**Last reviewed:** 2026-06-05
**Related files:** `../../.env.example`, `../../config/bot.php`, `../../src/ConfigValidator.php`, `../../tests/ConfigValidatorTest.php`
**Related docs:** [Component inventory](component-inventory.md), [Configuration](../01-user-guides/configuration.md), [Environment management](../02-operator-guides/environment-management.md), [Configuration reference](../03-technical-reference/configuration-reference.md)

| Variable | Required | Default | Validation | Exposed to Discord? |
| --- | --- | --- | --- | --- |
| `DISCORD_BOT_TOKEN` | Yes | Blank placeholder only. | Required, no whitespace/control characters, token-like characters. | No; never print it. |
| `BOT_PREFIX` | Yes | `!bot` | Non-empty, no whitespace/control characters, max 32 characters. | Yes, in usage/help/settings output. |
| `BOT_TIMEZONE` | No | `America/Toronto` | Must be a PHP timezone identifier. | Yes, in `time` and `settings`. |
| `APP_ENV` | No | `local` | `local`, `testing`, `staging`, or `production`. | Yes, in `settings`. |
| `LOG_LEVEL` | No | `debug` | `debug`, `info`, `warning`, or `error`. | No direct command output. |
| `LOG_FILE_ENABLED` | No | `true` | Boolean-like value. | No direct command output. |
| `LOG_FILE_DIR` | No | `storage/logs` | Non-empty printable path. | No direct command output. |
| `BOT_ENABLE_PREFIX_COMMANDS` | No | `true` | Boolean-like value; at least one interaction path must be enabled. | Enables or disables public prefix command replies. |
| `BOT_ENABLE_SLASH_COMMANDS` | No | `false` | Boolean-like value. | Enables or disables private ephemeral slash replies. |
| `BOT_ENABLE_MENTION_COMMANDS` | No | `false` | Boolean-like value. | Enables or disables public mention command replies. |
| `BOT_ENABLE_DM_COMMANDS` | No | `false` | Boolean-like value. | Enables or disables private DM command replies. |
| `BOT_RATE_LIMIT_MAX_ATTEMPTS` | No | `5` | Integer `0` or a positive integer; non-integer values are rejected. | Controls basic per-user command limiting. |
| `BOT_RATE_LIMIT_WINDOW_SECONDS` | No | `10` | Positive integer; non-integer values are rejected. | Controls the basic rate-limit window. |

## Source-of-truth note

Update this table whenever `.env.example`, `config/bot.php`, or `ConfigValidator` changes. Boolean-like values accept `true`, `false`, `1`, `0`, `yes`, `no`, `on`, and `off`.
