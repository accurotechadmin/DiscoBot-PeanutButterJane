# Master Index

**Audience:** All readers needing a single lookup page.
**Status:** Current reference
**Last reviewed:** 2026-06-05
**Related files:** `../../README.md`, `../../docs/`, `../../bin/bot.php`, `../../config/`, `../../src/`, `../../tests/`
**Related docs:** [Documentation home](../README.md), [Documentation map](../00-start-here/documentation-map.md), [Component inventory](component-inventory.md), [File and directory reference](../03-technical-reference/file-and-directory-reference.md)

| Need | Go to | Source anchor |
| --- | --- | --- |
| Understand the app quickly | [Application at a glance](../00-start-here/application-at-a-glance.md) | `../../bin/bot.php`, `../../src/Bot.php` |
| Inventory implemented components | [Component inventory](component-inventory.md) | `../../bin/`, `../../config/`, `../../src/`, `../../tests/` |
| Fresh checkout to production-server operation | [Authoritative installation, startup, and configuration guide](../01-user-guides/installation-startup-configuration-guide.md) | `../../README.md`, `../../.env.example`, `../../composer.json`, `../../bin/bot.php`, `../../bin/sync-slash-commands.php`, `../../config/bot.php`, `../../src/ConfigValidator.php` |
| Fresh checkout to responding bot | [From GitHub to a running bot](../01-user-guides/from-github-to-running-bot.md) | `../../README.md`, `../../composer.json`, `../../.env.example`, `../../bin/bot.php`, `../../config/commands.php` |
| Install and run | [Quick start](../01-user-guides/quick-start.md), [Authoritative installation guide](../01-user-guides/installation-startup-configuration-guide.md) | `../../README.md`, `../../composer.json` |
| Configure environment | [Configuration](../01-user-guides/configuration.md) | `../../.env.example`, `../../config/bot.php` |
| Operate the process | [Operator guides](../02-operator-guides/README.md) | `../../bin/bot.php`, `../../src/ConsoleLogger.php` |
| Trace runtime lifecycle | [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md) | `../../bin/bot.php`, `../../src/Bot.php` |
| Verify parsing | [Command routing reference](../03-technical-reference/command-routing-reference.md), [Interaction paths reference](../03-technical-reference/interaction-paths-reference.md) | `../../src/CommandRouter.php`, `../../tests/CommandRouterTest.php` |
| Add commands | [Adding a command](../04-extensibility/adding-a-command.md) | `../../src/Commands/CommandInterface.php`, `../../config/commands.php` |
| Copy examples | [Examples](../05-examples/README.md) | `../../src/Commands/`, `../../tests/` |
| Explore a delivery-business bot concept | [Delivery operations bot blueprint](../05-examples/delivery-operations-bot-blueprint.md) | Future planning example; current implementation remains the skeleton source and tests. |
| Catalogue delivery-business data requirements | [Delivery operations data requirements index](../05-examples/delivery-operations-data-requirements-index.md) | Future planning example mapping proposed outputs to source data, formulas, sensitivity, and command consumers. |
| Initialize a coding LLM session or maintain docs | [Coding LLM session primer](../06-maintainer-guides/coding-llm-session-primer.md), [Documentation maintenance](../06-maintainer-guides/documentation-maintenance.md), [Documentation study report](../06-maintainer-guides/documentation-study-report.md), [Documentation audit report](../06-maintainer-guides/documentation-audit-report.md) | `../../docs/` |
| Review decisions | [Decision records index](decision-records-index.md) | `../08-architecture-decisions/` |
