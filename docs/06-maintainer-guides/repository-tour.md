# Repository Tour

**Audience:** Maintainers orienting themselves in the codebase.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../README.md`, `../../composer.json`, `../../bin/bot.php`, `../../config/`, `../../src/`, `../../tests/`, `../../docs/`
**Related docs:** [File and directory reference](../03-technical-reference/file-and-directory-reference.md), [Architecture overview](../03-technical-reference/architecture-overview.md), [Master index](../07-reference/master-index.md)

This repository is organized around a single CLI Discord bot runtime and a documentation set that explains it.

| Area | Maintainer purpose |
| --- | --- |
| `../../README.md` | Concise project overview and link to full docs. |
| `../../composer.json` | PHP version requirement, DiscordPHP dependency, autoloading, and scripts. |
| `../../bin/bot.php` | Startup sequence and process entrypoint. |
| `../../config/bot.php` | Environment-backed bot settings. |
| `../../config/commands.php` | Canonical command registry and aliases. |
| `../../src/Bot.php` | DiscordPHP client options, enabled interaction listeners, guards, basic rate-limit checks, lifecycle integration, and public/private/ephemeral reply sending. |
| `../../src/CommandRouter.php` | Prefix/mention/DM parsing, slash dispatch, alias handling, metadata validation, metadata injection, and safe dispatch. |
| `../../src/CommandContext.php` | Command input object with nullable DiscordPHP references. |
| `../../src/Commands/` | Built-in commands, command interface, and usage formatting helper. |
| `../../tests/` | PHPUnit coverage for parser, built-ins, config, logger, rate limiter, and lifecycle. |
| `../../docs/` | Current-state documentation, examples, references, and ADRs. |

## Before editing

- Read the relevant source file first.
- Check the matching tests for expected behavior.
- Search docs for the concept you are changing.
- Decide whether an ADR or reference index needs an update.
