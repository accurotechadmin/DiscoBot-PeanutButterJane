# Logging and Log Levels

**Audience:** Operators running the bot locally or in a long-lived shell.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../config/bot.php`, `../../src/ConsoleLogger.php`, `../../storage/logs/.gitkeep`, `../../.env.example`
**Related docs:** [Logging reference](../03-technical-reference/logging-reference.md), [Running in long-lived sessions](running-in-long-lived-sessions.md), [ADR 0004](../08-architecture-decisions/adr-0004-console-only-logging.md)

Set `LOG_LEVEL` in `.env` to choose the minimum emitted severity:

```dotenv
LOG_LEVEL=debug
LOG_FILE_ENABLED=true
LOG_FILE_DIR=storage/logs
```

Accepted levels are `debug`, `info`, `warning`, and `error`. The default is `debug`.

## Console output

The logger writes timestamped lines to STDOUT after startup validation creates the logger. Startup errors before logger construction still go to STDERR.

## Structured file output

When `LOG_FILE_ENABLED=true`, the logger also appends one JSON object per line to `LOG_FILE_DIR/bot-YYYY-MM-DD.json`. A new filename is used for each local date, so a day covers `00:00:00` through `23:59:59`.

Generated JSON files in `storage/logs/` are ignored by Git. `storage/logs/.gitkeep` exists only so the default directory is present in a fresh checkout.

## Operational note

This is still intentionally small logging: there is no metrics backend, alerting, or external log aggregation in this repository. **Future consideration:** add those only with source changes, tests, and operator guidance.
