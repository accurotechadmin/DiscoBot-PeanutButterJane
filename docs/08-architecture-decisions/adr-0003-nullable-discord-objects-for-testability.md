# ADR 0003: Nullable Discord Objects for Testability

**Audience:** Maintainers reviewing architectural tradeoffs.
**Status:** Accepted
**Last reviewed:** 2026-06-03
**Related files:** `../../src/CommandContext.php`, `../../src/CommandRouter.php`, `../../tests/CommandRouterTest.php`, `../../tests/BuiltInCommandsTest.php`
**Related docs:** [Decision records index](../07-reference/decision-records-index.md), [Command context reference](../03-technical-reference/command-context-reference.md), [Safe DiscordPHP object usage](../04-extensibility/safe-discordphp-object-usage.md)

## Context

Commands should be unit-testable without a live Discord connection or complex DiscordPHP fakes. Many commands only need command name, arguments, prefix, and config. Production routing still supplies live DiscordPHP objects through `CommandRouter::route()`.

## Decision

Allow `App\CommandContext` to carry nullable `Discord\Discord` and `Discord\Parts\Channel\Message` objects.

Commands can check `hasDiscord()`, `hasMessage()`, `discord()`, and `message()` before using DiscordPHP-specific APIs.

## Consequences

- Direct command tests can construct `CommandContext` with `discord: null` and `message: null`.
- `CommandRouter::routeContent()` remains useful for offline parser and dispatch tests.
- Command authors must guard DiscordPHP object access.
- Commands that need Discord-specific details should provide a clear fallback for offline contexts.

## Alternatives considered

- Always requiring DiscordPHP objects would make tests heavier and couple command tests to DiscordPHP internals.
- Building repository-owned fake Discord message objects would add maintenance overhead.
- Hiding DiscordPHP entirely would make advanced commands harder to write.

## Related files

- `../../src/CommandContext.php`
- `../../src/CommandRouter.php`
- `../../tests/CommandRouterTest.php`
- `../../tests/BuiltInCommandsTest.php`
