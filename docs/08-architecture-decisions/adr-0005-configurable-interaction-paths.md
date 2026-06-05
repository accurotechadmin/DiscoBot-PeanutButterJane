# ADR 0005: Configurable Interaction Paths

**Audience:** Maintainers reviewing interaction entry point tradeoffs.
**Status:** Accepted
**Last reviewed:** 2026-06-04
**Related files:** `../../config/bot.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../src/ConfigValidator.php`, `../../src/Commands/CommandUsage.php`, `../../tests/CommandRouterTest.php`, `../../tests/ConfigValidatorTest.php`
**Related docs:** [Interaction paths reference](../03-technical-reference/interaction-paths-reference.md), [Command routing reference](../03-technical-reference/command-routing-reference.md), [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md)

## Context

The skeleton originally focused on prefix commands. The bot now needs to support additional user interaction paths while preserving the original prefix behavior and keeping command classes reusable.

The owner must be able to choose which paths are active without editing PHP source.

## Decision

Support four independently configurable interaction paths:

- prefix commands, enabled by default for backward compatibility;
- slash commands, disabled by default;
- bot mention commands, disabled by default;
- direct message commands, disabled by default.

Keep command execution centralized in `App\CommandRouter`. Message-based paths parse text into command name plus whitespace-split arguments. Slash commands use Discord interactions, respond ephemerally, and route through the same command execution method.

## Consequences

- Existing `!bot` style behavior remains the default.
- Server owners can opt into slash, mention, and DM command paths through `.env` settings.
- Slash replies are private to the invoking user through ephemeral interaction responses.
- Mention and prefix replies remain public server-channel messages.
- DM replies remain private direct-message conversations.
- Command classes continue to implement the same `CommandInterface` contract.
- Slash command options are intentionally generic today; only `echo` receives an optional `arguments` string option.

## Alternatives considered

- Replacing prefix routing with slash commands only. Rejected because it would break the skeleton's existing behavior and tests.
- Creating separate command classes for each path. Rejected because the current command contract is simple and reusable.
- Adding a full typed slash-option metadata system now. Rejected to keep the skeleton lightweight.

**Future consideration:** add per-command slash option metadata if commands need typed Discord-native options beyond the current generic `arguments` string.
