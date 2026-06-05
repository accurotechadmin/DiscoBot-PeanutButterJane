# Authoritative Installation, Startup, and Configuration Guide

**Audience:** Users and operators who need one source-aligned path from a GitHub checkout to a running DiscordPHP bot process on a local machine, staging host, or production server.
**Status:** Current guide
**Last reviewed:** 2026-06-05
**Related files:** `../../README.md`, `../../.env.example`, `../../composer.json`, `../../phpunit.xml`, `../../bin/bot.php`, `../../bin/sync-slash-commands.php`, `../../config/bot.php`, `../../config/commands.php`, `../../src/Bot.php`, `../../src/ConfigValidator.php`, `../../src/ConsoleLogger.php`, `../../src/RateLimiter.php`, `../../src/RuntimeLifecycle.php`, `../../src/SlashCommandSynchronizer.php`, `../../storage/logs/.gitkeep`
**Related docs:** [From GitHub to a running bot](from-github-to-running-bot.md), [Installation](installation.md), [Quick start](quick-start.md), [Configuration](configuration.md), [Inviting the bot to Discord](inviting-the-bot-to-discord.md), [Interaction paths](interaction-paths.md), [Running the bot](running-the-bot.md), [Troubleshooting](troubleshooting.md), [Operator guides](../02-operator-guides/README.md), [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md), [Configuration reference](../03-technical-reference/configuration-reference.md), [Environment variable index](../07-reference/environment-variable-index.md)

This is the single authoritative operational path for the current repository. It consolidates the user, operator, technical, reference, and ADR guidance for installation, startup, configuration, and server operation while keeping the repository's current boundaries explicit.

Current behavior: this application is a lightweight PHP 8.1+ CLI Discord bot skeleton powered by DiscordPHP. It is not a Laravel or Symfony application, does not include a database, queue, web controller, Dockerfile, systemd unit, hosted-platform manifest, monitoring stack, external process manager configuration, or log aggregation pipeline. On a production server, this repository still starts the bot as `php bin/bot.php` or `composer bot`; any supervisor, deployment platform, secret manager, or log aggregation system is external operational infrastructure around that command.

## 1. What you are installing

The bot is a long-running PHP CLI process. DiscordPHP owns the event loop, and repository startup is intentionally small:

1. `bin/bot.php` requires `vendor/autoload.php` and exits early with a clear STDERR message if Composer dependencies are missing.
2. If `.env` exists and is readable, the bootstrap loads values that are not already present in the process environment.
3. `config/bot.php` reads environment-derived bot settings.
4. `config/commands.php` reads the command registry.
5. `ConfigValidator::validateStartupConfig()` checks token, prefix, timezone, environment, log level, logging settings, interaction toggles, rate-limit settings, command registry entries, aliases, and slash option metadata before DiscordPHP connects.
6. `ConsoleLogger`, `CommandRouter`, and `RuntimeLifecycle` are created.
7. `Bot` creates the DiscordPHP client, enables intents required by the configured interaction paths, installs lightweight signal handling when `pcntl` is available, registers message and slash listeners, applies per-user rate limiting before dispatch, and sends replies through Discord.

Slash command definition synchronization is a separate operation. Normal bot startup can listen for slash interactions when enabled, but it does not write slash command definitions to Discord.

## 2. Requirements

Install or provide these before starting the bot:

- PHP 8.1.2 or newer.
- Composer.
- Network access from the running host to Discord.
- A Discord application with a bot user and token.
- Discord's **Message Content Intent** enabled in the Developer Portal for prefix, mention, and direct-message command paths.
- File-system write access to `LOG_FILE_DIR` if structured daily JSON logs are enabled.

Composer dependencies are declared in `composer.json`:

- runtime: `team-reflex/discord-php`;
- development/test: `phpunit/phpunit`.

The test suite is offline, but it uses `vendor/autoload.php`; install Composer dependencies before running PHPUnit.

## 3. Clone or prepare the repository

From a new host or checkout:

```bash
git clone <your-repository-url>
cd DiscoBot-PeanutButterJane
```

If you are using this skeleton as a template for a new project, keep the runtime boundary the same unless you intentionally change and document the architecture: CLI-first, framework-free, DiscordPHP-centered.

## 4. Install Composer dependencies

Run:

```bash
composer install
```

This creates `vendor/autoload.php`, which both runtime entrypoints require. If `vendor/autoload.php` is missing, `php bin/bot.php` prints a Composer dependency error and exits before reading runtime configuration.

For development or pre-deployment verification, the existing Composer scripts are:

```bash
composer lint
composer test
composer check
```

`composer check` runs lint first and then PHPUnit. If dependencies are absent, `composer test` cannot find `vendor/bin/phpunit`; install dependencies before treating tests as runnable.

## 5. Create the Discord application and invite the bot

1. Open the Discord Developer Portal.
2. Create or select an application.
3. Add a bot user under **Bot**.
4. Copy the bot token and store it only in `.env` or an external secret source. Never commit the token.
5. Enable **Message Content Intent** if any of these configured paths will be used: prefix commands, mention commands, or direct-message commands.
6. Use **OAuth2 -> URL Generator**.
7. Select the `bot` scope and permissions such as **Send Messages**, **Read Message History**, and **View Channels**.
8. If slash commands will be enabled, also select the `applications.commands` scope.
9. Open the generated URL and invite the bot to the target server.

For least surprise while first validating the bot, start with prefix commands enabled and slash, mention, and DM paths left at their defaults. Enable optional paths after the basic `!bot ping` flow works.

## 6. Configure environment values

For local development, copy the sample file:

```bash
cp .env.example .env
```

Then edit `.env`. For shared staging or production hosts, prefer externally injected environment variables or mounted secret files if your platform provides them. The repository bootstrap supports both: process environment values win, and `.env` only fills in missing values.

A complete current example is:

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

### Environment variable table

| Variable | Default | Validation and effect |
| --- | --- | --- |
| `DISCORD_BOT_TOKEN` | none | Required. Must be non-blank, token-like, and contain no whitespace or control characters. |
| `BOT_PREFIX` | `!bot` | Required effective value. Must be non-empty, contain no spaces, contain no control characters, and be 32 characters or fewer. |
| `BOT_TIMEZONE` | `America/Toronto` | Must be a valid PHP timezone identifier such as `UTC`, `America/Toronto`, or `Europe/London`. Used by the `time` command. |
| `APP_ENV` | `local` | Must be one of `local`, `testing`, `staging`, or `production`. Displayed by the safe `settings` command. |
| `LOG_LEVEL` | `debug` | Must be `debug`, `info`, `warning`, or `error`. Filters console and structured file output. |
| `LOG_FILE_ENABLED` | `true` | Boolean-like value. Enables or disables generated daily structured JSON log files. |
| `LOG_FILE_DIR` | `storage/logs` | Non-empty printable directory path. Daily files are named `bot-YYYY-MM-DD.json`. |
| `BOT_ENABLE_PREFIX_COMMANDS` | `true` | Boolean-like value. Enables public prefix commands such as `!bot ping`. |
| `BOT_ENABLE_SLASH_COMMANDS` | `false` | Boolean-like value. Enables slash command listeners and ephemeral slash replies. Definitions must still be synchronized explicitly. |
| `BOT_ENABLE_MENTION_COMMANDS` | `false` | Boolean-like value. Enables public bot-mention commands. |
| `BOT_ENABLE_DM_COMMANDS` | `false` | Boolean-like value. Enables private direct-message commands without a prefix. |
| `BOT_RATE_LIMIT_MAX_ATTEMPTS` | `5` | Integer. `0` disables the basic per-user rate limiter; positive values enforce a maximum per window. |
| `BOT_RATE_LIMIT_WINDOW_SECONDS` | `10` | Positive integer. Controls the basic rate-limit window size. |

Boolean-like values accepted by validation are `1`, `true`, `yes`, `on`, `0`, `false`, `no`, and `off`.

At least one interaction path must be enabled. With defaults, prefix commands are enabled and the other paths are disabled.

## 7. Choose interaction paths and Discord intents

| Path | Default | User input | Reply visibility | Discord setup notes |
| --- | --- | --- | --- | --- |
| Prefix commands | Enabled | `!bot ping` | Public channel reply | Requires Message Content Intent and normal bot message permissions. |
| Slash commands | Disabled | `/ping` | Ephemeral reply visible to the invoking user | Requires `applications.commands` invite scope and explicit synchronization. |
| Mention commands | Disabled | `@Bot ping` | Public channel reply | Requires Message Content Intent and normal bot message permissions. |
| Direct-message commands | Disabled | `ping` in a one-to-one DM | Private DM reply | Requires Message Content Intent; the runtime adds Direct Messages intent when this path is enabled. |

Message-based paths need message content access because Discord treats message content as privileged gateway data. Slash commands do not require Message Content Intent for command content, but their definitions still need Discord-side synchronization.

## 8. Validate startup before Discord connects

Startup validation runs every time `bin/bot.php` or `bin/sync-slash-commands.php` reaches configuration loading. Common fail-fast checks include:

- missing or malformed `DISCORD_BOT_TOKEN`;
- blank, spaced, overlong, or control-character-containing `BOT_PREFIX`;
- invalid `BOT_TIMEZONE`;
- unsupported `APP_ENV` or `LOG_LEVEL`;
- invalid boolean toggles;
- all interaction paths disabled;
- invalid rate-limit integers;
- invalid command registry entries, aliases, or slash option metadata.

If startup fails before logger construction, the entrypoint writes an error line to STDERR and exits with status code `1`. Fix configuration first; do not troubleshoot Discord gateway behavior until validation passes.

## 9. Run the bot locally or on a server

Start the process directly:

```bash
php bin/bot.php
```

Or with Composer:

```bash
composer bot
```

Both paths run the same bootstrap. A successful run should log bootstrapping details, enabled interaction paths, and Discord readiness. The process stays running while DiscordPHP owns the event loop.

Stop the process with your terminal or external supervisor. When the `pcntl` extension is available, `RuntimeLifecycle` installs SIGINT and SIGTERM handlers and requests Discord runtime shutdown. Without `pcntl`, the bot still runs, but signal handling logs a warning and depends on normal process termination.

## 10. Synchronize slash commands when needed

If `BOT_ENABLE_SLASH_COMMANDS=true`, startup listens for slash interactions, but Discord must know the command definitions first. Run synchronization after enabling slash commands or changing `config/commands.php` slash metadata:

```bash
composer sync-slash-commands
```

Equivalent direct command:

```bash
php bin/sync-slash-commands.php
```

The synchronizer connects to DiscordPHP, builds definitions from `CommandRouter::slashCommandDefinitions()`, saves them to the Discord application command collection, logs each result, and closes the Discord client when supported by the installed DiscordPHP version.

## 11. Check that the bot responds

With the default prefix configuration, send this in a server channel where the bot can read and send messages:

```text
!bot ping
```

Expected reply:

```text
Pong!
```

A bare prefix such as `!bot` routes to help. Command names are case-insensitive. Prefix-adjacent text such as `!botping` is ignored so ordinary words are not mistaken for bot commands.

Useful first checks:

```text
!bot help
!bot settings
!bot time
!bot echo hello world
```

The `settings` command intentionally shows safe non-secret settings only: prefix, timezone, and environment.

## 12. Production-server operation within current repository boundaries

For a production server, set `APP_ENV=production`, provide a real token through a secure secret mechanism, and run the same CLI command. The repository does not prescribe how your server keeps that process alive.

A production-oriented checklist:

1. Install PHP 8.1.2+ and Composer on the host.
2. Deploy the repository files.
3. Run `composer install` so `vendor/autoload.php` exists.
4. Provide `DISCORD_BOT_TOKEN` securely. Prefer external secret injection for shared hosts; use `.env` only when that is appropriate for your environment.
5. Set `APP_ENV=production` and choose `LOG_LEVEL` intentionally, often `info` or stricter if you do not need debug output.
6. Confirm `LOG_FILE_ENABLED` and `LOG_FILE_DIR` match the host's file permissions and retention policy.
7. Enable only the interaction paths you intend to support.
8. Enable Message Content Intent in Discord for prefix, mention, or DM commands.
9. Invite the bot with required permissions and `applications.commands` scope if slash commands are enabled.
10. Run `composer sync-slash-commands` after enabling slash commands or changing slash metadata.
11. Start `php bin/bot.php` or `composer bot` under your chosen external session or supervision mechanism.
12. Watch STDOUT/STDERR and generated daily JSON logs for startup validation, readiness, reply failures, rate-limit warnings, and shutdown messages.
13. Stop the PHP process gracefully before deploying updates, then run `composer install` again if dependencies changed and restart the process.

Current behavior: generated JSON logs are local files only. If production requires log retention, central aggregation, alerts, health checks, process restart policies, backups, or deployment manifests, provide those outside this repository unless a future task explicitly adds and documents repository-owned infrastructure.

**Future consideration:** add first-party deployment manifests, process-manager examples, health checks, or monitoring integrations only with matching source or configuration files, tests or verification guidance, and updates across the operator and architecture documentation.

## 13. Troubleshooting startup and first run

| Symptom | Likely cause | First fix |
| --- | --- | --- |
| `Missing Composer dependencies` before startup | `vendor/autoload.php` is missing. | Run `composer install`. |
| Error about `DISCORD_BOT_TOKEN` | Token is blank, missing from process env and `.env`, or malformed. | Set a real token without whitespace. |
| Error about prefix, timezone, environment, log level, logging, interactions, or rate limit | Startup validation rejected configuration. | Compare `.env` with the table in this guide and `.env.example`. |
| Process starts but prefix commands do not reply | Message Content Intent, invite permissions, channel access, prefix mismatch, or bot not actually online. | Confirm Discord Developer Portal intent, OAuth permissions, channel permissions, and exact prefix. |
| Slash commands do not appear | Definitions were not synchronized or bot was invited without `applications.commands`. | Run `composer sync-slash-commands` and verify invite scopes. |
| Slash listener is enabled but command behavior is stale | Discord-side definitions may not match `config/commands.php`. | Re-run synchronization after registry or slash metadata changes. |
| Logs are missing from files | File logging disabled or `LOG_FILE_DIR` is not writable. | Check `LOG_FILE_ENABLED`, directory path, and permissions. |
| Rate-limit reply appears | User exceeded `BOT_RATE_LIMIT_MAX_ATTEMPTS` in the configured window. | Wait for the window or tune the rate-limit settings. |

## 14. Source information documents used

This guide was consolidated from these repository source-information documents and source files:

### Root and executable source of truth

- `../../README.md`
- `../../.env.example`
- `../../composer.json`
- `../../phpunit.xml`
- `../../bin/bot.php`
- `../../bin/sync-slash-commands.php`
- `../../config/bot.php`
- `../../config/commands.php`
- `../../src/Bot.php`
- `../../src/CommandRouter.php`
- `../../src/ConfigValidator.php`
- `../../src/ConsoleLogger.php`
- `../../src/RateLimiter.php`
- `../../src/RuntimeLifecycle.php`
- `../../src/SlashCommandSynchronizer.php`
- `../../src/Commands/SettingsCommand.php`

### Documentation maps and maintenance docs

- [Documentation home](../README.md)
- [Start here overview](../00-start-here/README.md)
- [Documentation map](../00-start-here/documentation-map.md)
- [User guides index](README.md)
- [Operator guides index](../02-operator-guides/README.md)
- [Technical reference index](../03-technical-reference/README.md)
- [Reference index](../07-reference/README.md)
- [Documentation maintenance](../06-maintainer-guides/documentation-maintenance.md)
- [Documentation audit report](../06-maintainer-guides/documentation-audit-report.md)

### User and operator guides

- [Application at a glance](../00-start-here/application-at-a-glance.md)
- [How to use these docs](../00-start-here/how-to-use-these-docs.md)
- [From GitHub to a running bot](from-github-to-running-bot.md)
- [Quick start](quick-start.md)
- [Installation](installation.md)
- [Configuration](configuration.md)
- [Inviting the bot to Discord](inviting-the-bot-to-discord.md)
- [Interaction paths](interaction-paths.md)
- [Running the bot](running-the-bot.md)
- [Troubleshooting](troubleshooting.md)
- [FAQ](faq.md)
- [Dependency management](../02-operator-guides/dependency-management.md)
- [Environment management](../02-operator-guides/environment-management.md)
- [Security and secrets](../02-operator-guides/security-and-secrets.md)
- [Startup validation](../02-operator-guides/startup-validation.md)
- [Logging and log levels](../02-operator-guides/logging-and-log-levels.md)
- [Running in long-lived sessions](../02-operator-guides/running-in-long-lived-sessions.md)

### Technical references, indexes, and ADRs

- [Architecture overview](../03-technical-reference/architecture-overview.md)
- [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md)
- [DiscordPHP integration](../03-technical-reference/discordphp-integration.md)
- [Configuration reference](../03-technical-reference/configuration-reference.md)
- [Interaction paths reference](../03-technical-reference/interaction-paths-reference.md)
- [Logging reference](../03-technical-reference/logging-reference.md)
- [Composer scripts reference](../03-technical-reference/composer-scripts-reference.md)
- [Error handling](../03-technical-reference/error-handling.md)
- [File and directory reference](../03-technical-reference/file-and-directory-reference.md)
- [Environment variable index](../07-reference/environment-variable-index.md)
- [Master index](../07-reference/master-index.md)
- [Data flow index](../07-reference/data-flow-index.md)
- [Component inventory](../07-reference/component-inventory.md)
- [ADR 0001: Lightweight CLI DiscordPHP Skeleton](../08-architecture-decisions/adr-0001-lightweight-cli-discordphp-skeleton.md)
- [ADR 0004: Lightweight Console and Daily JSON Logging](../08-architecture-decisions/adr-0004-console-only-logging.md)
- [ADR 0005: Configurable Interaction Paths](../08-architecture-decisions/adr-0005-configurable-interaction-paths.md)

## 15. Quick command summary

```bash
composer install
cp .env.example .env
composer lint
composer test
php bin/bot.php
composer bot
composer sync-slash-commands
php bin/sync-slash-commands.php
```

Run `composer test` only after dependencies are installed. Run slash synchronization only when slash commands are enabled or slash command definitions changed.
