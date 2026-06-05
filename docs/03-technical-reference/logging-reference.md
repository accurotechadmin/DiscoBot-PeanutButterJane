# Logging Reference

**Audience:** Maintainers and operators verifying logger behavior.
**Status:** Current reference
**Last reviewed:** 2026-06-04
**Related files:** `../../src/ConsoleLogger.php`, `../../tests/ConsoleLoggerTest.php`, `../../bin/bot.php`, `../../config/bot.php`, `../../storage/logs/.gitkeep`
**Related docs:** [Logging and log levels](../02-operator-guides/logging-and-log-levels.md), [Error handling](error-handling.md), [ADR 0004](../08-architecture-decisions/adr-0004-console-only-logging.md)

`App\ConsoleLogger` is an invokable logger used by bootstrap, bot runtime code, slash synchronization, and router exception handling. It writes filtered console lines and, when `LOG_FILE_ENABLED=true`, appends the same events as structured JSON records under `LOG_FILE_DIR`.

## Console format

Each emitted console line is written with:

```text
[YYYY-MM-DD HH:MM:SS] LEVEL: message
```

The default stream is STDOUT. Tests can inject another stream resource, as shown by `../../tests/ConsoleLoggerTest.php`.

## Structured daily JSON files

When file logging is enabled, each emitted log event is appended as one JSON object per line to:

```text
LOG_FILE_DIR/bot-YYYY-MM-DD.json
```

The file date is derived from the emitted timestamp, so each local day starts at `00:00:00` and ends at `23:59:59`. Generated JSON log files under `storage/logs/` are ignored by Git.

Each record contains `timestamp`, `level`, and `message`.

## Filtering

| Configured minimum | Emitted levels |
| --- | --- |
| `debug` | `debug`, `info`, `warning`, `error` |
| `info` | `info`, `warning`, `error` |
| `warning` | `warning`, `error` |
| `error` | `error` |

The constructor rejects unknown configured minimum levels. Unknown per-message levels are normalized for display but filtered as `info` severity.

## Call sites

| Caller | Example purpose |
| --- | --- |
| `../../bin/bot.php` | Startup mode, prefix/timezone, Message Content Intent reminder. |
| `../../bin/sync-slash-commands.php` | Slash command synchronization progress and warnings. |
| `../../src/Bot.php` | Connecting, ready state, lifecycle, rate-limit/send-reply warnings. |
| `../../src/CommandRouter.php` | Command exception warnings with safe user-facing replies. |
