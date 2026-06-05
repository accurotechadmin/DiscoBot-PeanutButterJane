# Extension Patterns

**Audience:** Developers deciding what belongs in this skeleton.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../src/Commands/`, `../../src/CommandContext.php`, `../../src/CommandRouter.php`, `../../config/commands.php`, `../../tests/`
**Related docs:** [Adding a command](adding-a-command.md), [Architecture overview](../03-technical-reference/architecture-overview.md), [ADR 0001](../08-architecture-decisions/adr-0001-lightweight-cli-discordphp-skeleton.md)

The project is intentionally lightweight. Prefer extensions that preserve direct readability, simple tests, and explicit registration.

## Fits well

- Stateless commands that return a string reply.
- Commands that read arguments from `$context->arguments()`.
- Commands that read safe config values from `$context->config()`.
- Small helper classes under `src/` when multiple commands need shared pure logic.
- PHPUnit tests that call commands directly or use `CommandRouter::routeContent()`.
- Aliases registered in `config/commands.php`.

## Be careful

Current behavior does not include dependency injection, database persistence, queues, framework lifecycle hooks, or background job infrastructure. **Future consideration:** add those only with a deliberate design decision, source changes, tests, and docs updates.

## Design questions before extending

| Question | Prefer yes when... |
| --- | --- |
| Can it be tested without Discord network calls? | The logic uses `CommandContext` and pure helpers. |
| Does it keep secrets out of replies? | Output is limited to safe values. |
| Does it fit the enabled interaction paths? | Users can invoke it as `<prefix> command args`, `/command`, `@Bot command args`, or `command args` in DMs depending on enabled paths. |
| Is registration explicit? | The command appears in `config/commands.php`. |
