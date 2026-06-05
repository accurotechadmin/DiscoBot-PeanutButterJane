# ADR 0001: Lightweight CLI DiscordPHP Skeleton

**Audience:** Maintainers reviewing architectural tradeoffs.
**Status:** Accepted
**Last reviewed:** 2026-06-03
**Related files:** `../../README.md`, `../../composer.json`, `../../bin/bot.php`, `../../src/Bot.php`, `../../config/bot.php`
**Related docs:** [Decision records index](../07-reference/decision-records-index.md), [Architecture overview](../03-technical-reference/architecture-overview.md), [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md)

## Context

The skeleton should be approachable, inspectable, and easy to run with `php bin/bot.php` or `composer bot`. DiscordPHP already provides the gateway client and event loop, so the repository does not need a framework shell to demonstrate prefix commands.

The intended audience includes learners, command authors, and maintainers who need a small base they can understand from source files and tests.

## Decision

Keep the application as a framework-free PHP CLI process powered by DiscordPHP.

`../../bin/bot.php` remains the process entrypoint. Configuration lives in small PHP config files, commands are explicitly registered, and DiscordPHP owns the long-running event loop.

## Consequences

- The codebase stays small and understandable.
- Startup and runtime behavior are easy to trace from `bin/bot.php` to `src/Bot.php` and `src/CommandRouter.php`.
- There is less infrastructure to configure for first-time users.
- Features such as web controllers, databases, queues, Docker-first deployment, and framework services are not available by default.
- Larger operational needs must be handled externally or added deliberately with tests and docs.

## Alternatives considered

- Laravel or Symfony application shells.
- A Docker-first stack.
- A hosted-platform template.
- A custom event-loop abstraction instead of direct DiscordPHP usage.

These alternatives are not implemented. **Future consideration:** add only with explicit source code, verification guidance, and docs/ADR updates.

## Related files

- `../../README.md`
- `../../composer.json`
- `../../bin/bot.php`
- `../../src/Bot.php`
- `../../config/bot.php`
