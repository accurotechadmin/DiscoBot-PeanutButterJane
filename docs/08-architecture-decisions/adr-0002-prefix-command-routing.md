# ADR 0002: Prefix Command Routing

**Audience:** Maintainers reviewing architectural tradeoffs.
**Status:** Accepted
**Last reviewed:** 2026-06-04
**Related files:** `../../config/commands.php`, `../../src/CommandRouter.php`, `../../src/ParsedCommand.php`, `../../tests/CommandRouterTest.php`
**Related docs:** [Decision records index](../07-reference/decision-records-index.md), [Command routing reference](../03-technical-reference/command-routing-reference.md), [Using prefix commands](../01-user-guides/using-prefix-commands.md)

## Context

The skeleton needs a simple command mechanism that works with Discord message events and is easy to test offline. Prefix commands map naturally to DiscordPHP `MESSAGE_CREATE` events and keep command examples readable.

The current parser behavior is covered in `../../tests/CommandRouterTest.php`, including whitespace handling, prefix-adjacent text, aliases, unknown commands, and exception safety.

## Decision

Keep prefix commands routed by `App\CommandRouter` as the default command model.

The configured prefix comes from `BOT_PREFIX`. A bare prefix routes to `help`, command names are normalized to lowercase, aliases resolve before lookup, and arguments are split on whitespace.

## Consequences

- Prefix routing is easy to understand and test.
- `CommandRouter::routeContent()` can test parsing and dispatch without Discord network calls.
- Users must type the configured prefix before commands.
- Discord Message Content Intent is required because the bot reads message text.
- The default prefix UX is not Discord-native slash command UX, but slash commands are now available as an opt-in path.

## Alternatives considered

Slash commands were not part of the original prefix-only design. They are now implemented as an opt-in interaction path; see ADR 0005.

**Future consideration:** add richer per-command slash option metadata if commands need typed Discord-native arguments.

## Related files

- `../../config/commands.php`
- `../../src/CommandRouter.php`
- `../../src/ParsedCommand.php`
- `../../tests/CommandRouterTest.php`
