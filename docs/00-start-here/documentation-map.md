# Documentation Map

**Audience:** All readers looking for the right page quickly.
**Status:** Current guide
**Last reviewed:** 2026-06-05
**Related files:** `../../README.md`, `../../composer.json`, `../../bin/bot.php`, `../../src/`, `../../tests/`
**Related docs:** [Documentation home](../README.md), [Master index](../07-reference/master-index.md), [Component inventory](../07-reference/component-inventory.md)

Use this page when you know your question but not the document name.

## By goal

| Goal | Start with | Then read |
| --- | --- | --- |
| Go from GitHub checkout to a responding bot | [Authoritative installation, startup, and configuration guide](../01-user-guides/installation-startup-configuration-guide.md) | [From GitHub to a running bot](../01-user-guides/from-github-to-running-bot.md), [Quick start](../01-user-guides/quick-start.md), [Installation](../01-user-guides/installation.md), [Configuration](../01-user-guides/configuration.md) |
| Run the bot once | [Quick start](../01-user-guides/quick-start.md) | [Authoritative installation guide](../01-user-guides/installation-startup-configuration-guide.md), [Installation](../01-user-guides/installation.md), [Configuration](../01-user-guides/configuration.md) |
| Invite the bot | [Inviting the bot to Discord](../01-user-guides/inviting-the-bot-to-discord.md) | [Troubleshooting](../01-user-guides/troubleshooting.md) |
| Use commands | [Interaction paths](../01-user-guides/interaction-paths.md) and [Using prefix commands](../01-user-guides/using-prefix-commands.md) | [Built-in commands](../01-user-guides/built-in-commands.md) |
| Operate a long-running process | [Authoritative installation, startup, and configuration guide](../01-user-guides/installation-startup-configuration-guide.md) | [Running in long-lived sessions](../02-operator-guides/running-in-long-lived-sessions.md), [Logging and log levels](../02-operator-guides/logging-and-log-levels.md) |
| Add a command | [Adding a command](../04-extensibility/adding-a-command.md) | [Examples](../05-examples/README.md), [Testing new commands](../04-extensibility/testing-new-commands.md) |
| Understand internals | [Architecture overview](../03-technical-reference/architecture-overview.md) | [Component inventory](../07-reference/component-inventory.md), [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md), [Command routing reference](../03-technical-reference/command-routing-reference.md) |
| Maintain docs or initialize an LLM session | [Documentation maintenance](../06-maintainer-guides/documentation-maintenance.md) | [Coding LLM session primer](../06-maintainer-guides/coding-llm-session-primer.md), [Documentation study report](../06-maintainer-guides/documentation-study-report.md), [How to use these docs](how-to-use-these-docs.md) |

## By role

| Role | Reading path |
| --- | --- |
| User | Start here -> authoritative installation guide -> GitHub-to-running guide -> quick start -> configuration -> built-in commands -> troubleshooting. |
| Operator | Authoritative installation guide -> environment -> secrets -> startup validation -> logging -> long-lived sessions -> dependency management. |
| Command author | Interface contract -> adding a command -> registration and aliases -> arguments/context -> examples -> tests. |
| Maintainer | Repository tour -> architecture -> component inventory -> command routing -> test suite -> release checklist -> ADRs. |
| Tester | Testing reference -> test suite tour -> example tests -> command routing reference. |
| Future documenter or coding LLM | Coding LLM session primer -> how to use these docs -> documentation maintenance -> documentation study report -> reference indexes -> ADR index. |

## Fast lookup

For dense lookup tables, including the component inventory, use [Reference](../07-reference/README.md). For decisions and tradeoffs, use [Architecture decisions](../08-architecture-decisions/README.md).
