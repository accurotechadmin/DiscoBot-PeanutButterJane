# Maintainer Guides

**Audience:** Future maintainers keeping code, tests, and docs aligned.
**Status:** Current guide
**Last reviewed:** 2026-06-05
**Related files:** `../../README.md`, `../../composer.json`, `../../bin/bot.php`, `../../config/`, `../../src/`, `../../tests/`, `../../docs/`
**Related docs:** [Repository tour](repository-tour.md), [Component inventory](../07-reference/component-inventory.md), [Code style and conventions](code-style-and-conventions.md), [Coding LLM session primer](coding-llm-session-primer.md), [Documentation maintenance](documentation-maintenance.md), [Documentation study report](documentation-study-report.md), [Documentation audit report](documentation-audit-report.md), [Test suite tour](test-suite-tour.md), [Release readiness checklist](release-readiness-checklist.md)

Use this section when changing the repository rather than simply running the bot.

## Maintainer map

| Page | Use it for |
| --- | --- |
| [Repository tour](repository-tour.md) | Understand the purpose of major files and directories. |
| [Component inventory](../07-reference/component-inventory.md) | Cross-check implemented components, sub-components, source, tests, docs, and connected pieces. |
| [Code style and conventions](code-style-and-conventions.md) | Preserve PHP and documentation style. |
| [Coding LLM session primer](coding-llm-session-primer.md) | Initialize a new coding LLM session with the best reading order and expected orientation report. |
| [Documentation maintenance](documentation-maintenance.md) | Keep Markdown pages source-aligned and cross-linked. |
| [Documentation study report](documentation-study-report.md) | Read a synthesized overview of the documentation set and the application it describes. |
| [Documentation audit report](documentation-audit-report.md) | Review the latest documentation-quality audit and maintenance backlog. |
| [Test suite tour](test-suite-tour.md) | Know which tests protect current behavior. |
| [Release readiness checklist](release-readiness-checklist.md) | Review before publishing or handing off changes. |

## High-value maintenance habits

- Treat source and tests as the documentation source of truth.
- Keep command examples synchronized with `CommandInterface` and `CommandContext`.
- Update user, operator, technical, reference, and ADR docs together when behavior changes.
- Run `composer check` when dependencies are available.
