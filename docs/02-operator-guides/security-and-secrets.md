# Security and Secrets

**Audience:** Operators and maintainers handling runtime credentials.
**Status:** Current guide
**Last reviewed:** 2026-06-03
**Related files:** `../../.env.example`, `../../.gitignore`, `../../config/bot.php`, `../../src/Commands/SettingsCommand.php`
**Related docs:** [Environment management](environment-management.md), [Configuration](../01-user-guides/configuration.md), [Environment variable index](../07-reference/environment-variable-index.md)

The only secret in the current configuration set is `DISCORD_BOT_TOKEN`. Keep it out of Git, issue trackers, screenshots, logs, and Discord messages.

## Current safety properties

- `.env.example` documents variable names but leaves `DISCORD_BOT_TOKEN` blank.
- `.gitignore` excludes local `.env` files.
- `../../config/bot.php` reads the token for runtime configuration.
- `../../src/Commands/SettingsCommand.php` shows prefix, timezone, and environment only; it does not print the token.
- Startup errors mention the missing token name, not a token value.

## Operator checklist

- Use `.env` for local development and externally injected secrets for shared environments.
- Rotate the Discord token immediately if it is exposed.
- Avoid pasting full environment dumps into support channels.
- Treat `APP_ENV` as a public label because the `settings` command displays it.
- Review new commands for accidental secret output before registering them.

## What is not provided

Current behavior: the repository does not implement a secrets manager, token rotation job, permission audit command, or external monitoring alert. **Future consideration:** add those only as explicit features or clearly documented external operational integrations.
