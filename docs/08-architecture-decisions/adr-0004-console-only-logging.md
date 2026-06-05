# ADR 0004: Lightweight Console and Daily JSON Logging

**Audience:** Maintainers reviewing architectural tradeoffs.
**Status:** Accepted
**Last reviewed:** 2026-06-04
**Related files:** `../../src/ConsoleLogger.php`, `../../bin/bot.php`, `../../bin/sync-slash-commands.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../tests/ConsoleLoggerTest.php`, `../../storage/logs/.gitkeep`
**Related docs:** [Decision records index](../07-reference/decision-records-index.md), [Logging reference](../03-technical-reference/logging-reference.md), [Logging and log levels](../02-operator-guides/logging-and-log-levels.md)

## Context

The skeleton needs visible startup, warning, and error output without introducing a logging framework. It also needs a basic retained local record for operators who run the bot from a normal shell and want a simple file trail.

## Decision

Use a tiny invokable logger that writes timestamped messages to STDOUT by default, filters by `debug`, `info`, `warning`, or `error` severity, and optionally appends one JSON object per line to a daily `bot-YYYY-MM-DD.json` file under `LOG_FILE_DIR`.

Startup errors that happen before logger construction are written to STDERR by `../../bin/bot.php` or `../../bin/sync-slash-commands.php`.

## Consequences

- The runtime remains lightweight and easy to inspect.
- Tests can inject a stream resource, a temporary directory, and a clock to verify filtering and daily JSON filenames.
- Generated JSON logs are ignored by Git.
- The repository still does not implement log aggregation, metrics, alerting, or an external monitoring stack.
- There is no PSR logger dependency today.

## Alternatives considered

A full logging framework, centralized log aggregation, and metrics backends are not implemented. **Future consideration:** add if operational requirements justify them and include source changes, tests, and updated operator guidance.

## Related files

- `../../src/ConsoleLogger.php`
- `../../bin/bot.php`
- `../../bin/sync-slash-commands.php`
- `../../src/Bot.php`
- `../../src/CommandRouter.php`
- `../../tests/ConsoleLoggerTest.php`
- `../../storage/logs/.gitkeep`
