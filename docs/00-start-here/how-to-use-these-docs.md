# How to Use These Docs

**Audience:** Readers and maintainers who want to interpret the documentation accurately.
**Status:** Current guide
**Last reviewed:** 2026-06-03
**Related files:** `../../README.md`, `../../bin/bot.php`, `../../config/commands.php`, `../../src/`, `../../tests/`
**Related docs:** [Documentation home](../README.md), [Documentation maintenance](../06-maintainer-guides/documentation-maintenance.md), [Master index](../07-reference/master-index.md)

These docs are written as current-state documentation for a small skeleton, not as a wish list for a larger framework.

## Status language

| Label | Meaning |
| --- | --- |
| Current | Implemented today and expected to match source code. |
| Current guide | Task-oriented guidance based on current behavior. |
| Current reference | Lookup or technical material based on current behavior. |
| Accepted | Architecture decision currently accepted by the project. |
| Maintained current-state documentation | A documentation hub kept synchronized with code. |

## Current behavior vs future ideas

Current behavior must be traceable to source files or tests. If a page mentions a feature such as Docker, queues, database persistence, file logging, or framework integration, it must state that it is absent today or label the idea exactly as **Future consideration**. Slash, mention, and DM command paths are current opt-in behavior and should be documented as implemented when enabled.

## Source paths in docs

Technical pages cite relative paths such as `../../src/CommandRouter.php` because the source code is the source of truth. User guides may link to technical references instead of repeating every implementation detail.

## Example snippets

Examples in [05 Examples](../05-examples/README.md) are documentation snippets. They are not active bot commands unless you add the class to `../../src/Commands/` and register it in `../../config/commands.php`.

## Keeping pages useful

Each page should answer a real reader question, include accurate metadata, and link to deeper references instead of duplicating large sections from other pages.
