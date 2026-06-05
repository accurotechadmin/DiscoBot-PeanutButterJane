# Documentation Home

**Audience:** Users, operators, command authors, maintainers, testers, and future documenters.
**Status:** Maintained current-state documentation
**Last reviewed:** 2026-06-05
**Related files:** `../README.md`, `../composer.json`, `../bin/bot.php`, `../config/bot.php`, `../config/commands.php`, `../src/`, `../tests/`
**Related docs:** [Start here](00-start-here/README.md), [Master index](07-reference/master-index.md), [Component inventory](07-reference/component-inventory.md), [Architecture decisions](08-architecture-decisions/README.md)

This documentation set explains the current DiscordPHP bot skeleton: a lightweight, framework-free PHP CLI process that can listen for configurable prefix, slash, mention, and direct-message command paths.

The source code is the source of truth. The docs intentionally describe what exists today and avoid implying that unimplemented infrastructure exists.

## Current project in one paragraph

The bot starts with `php bin/bot.php` or `composer bot`, loads optional `.env` values, validates configuration, creates a console and daily JSON-capable logger, builds a command router, constructs the DiscordPHP client with required intents, installs basic lifecycle handling, applies basic rate limiting to command-like interactions, routes enabled command interactions through command classes, and sends public, private, or ephemeral replies according to the interaction path. See `../bin/bot.php`, `../src/Bot.php`, and `../src/CommandRouter.php` for the runtime path.

## Section map

| Section | What it answers |
| --- | --- |
| [00 Start here](00-start-here/README.md) | What the app is, what it is not, vocabulary, and how to read the docs. |
| [01 User guides](01-user-guides/README.md) | How to go from GitHub checkout to running bot, install, configure, invite, run, use commands, and troubleshoot. |
| [02 Operator guides](02-operator-guides/README.md) | How to manage environment, secrets, dependencies, logs, startup, and long-lived process operation. |
| [03 Technical reference](03-technical-reference/README.md) | Source-aligned details for lifecycle, architecture, configuration, routing, context, DiscordPHP, errors, logging, testing, scripts, and files. |
| [04 Extensibility](04-extensibility/README.md) | How to add prefix commands that match the existing command interface and test style. |
| [05 Examples](05-examples/README.md) | Copyable command, registration, context, config, Discord message, and PHPUnit snippets. |
| [06 Maintainer guides](06-maintainer-guides/README.md) | Repository tour, conventions, LLM primer, documentation maintenance, study/audit reports, tests, and release readiness. |
| [07 Reference](07-reference/README.md) | Dense lookup tables for component inventory, files, classes, commands, environment variables, data flows, ADRs, and glossary terms. |
| [08 Architecture decisions](08-architecture-decisions/README.md) | Accepted lightweight ADRs explaining current design choices and tradeoffs. |

## Recommended reading paths

### Users
1. [Application at a glance](00-start-here/application-at-a-glance.md)
2. [From GitHub to a running bot](01-user-guides/from-github-to-running-bot.md)
3. [Quick start](01-user-guides/quick-start.md)
4. [Installation](01-user-guides/installation.md)
5. [Configuration](01-user-guides/configuration.md)
6. [Running the bot](01-user-guides/running-the-bot.md)
7. [Built-in commands](01-user-guides/built-in-commands.md)

### Operators
1. [Environment management](02-operator-guides/environment-management.md)
2. [Security and secrets](02-operator-guides/security-and-secrets.md)
3. [Startup validation](02-operator-guides/startup-validation.md)
4. [Logging and log levels](02-operator-guides/logging-and-log-levels.md)
5. [Running in long-lived sessions](02-operator-guides/running-in-long-lived-sessions.md)

### Command authors
1. [Adding a command](04-extensibility/adding-a-command.md)
2. [Command interface contract](04-extensibility/command-interface-contract.md)
3. [Command arguments and context](04-extensibility/command-arguments-and-context.md)
4. [Command registration and aliases](04-extensibility/command-registration-and-aliases.md)
5. [Examples](05-examples/README.md)
6. [Testing new commands](04-extensibility/testing-new-commands.md)

### Maintainers
1. [Repository tour](06-maintainer-guides/repository-tour.md)
2. [Architecture overview](03-technical-reference/architecture-overview.md)
3. [Command routing reference](03-technical-reference/command-routing-reference.md)
4. [Interaction paths reference](03-technical-reference/interaction-paths-reference.md)
5. [Component inventory](07-reference/component-inventory.md)
6. [Test suite tour](06-maintainer-guides/test-suite-tour.md)
7. [Release readiness checklist](06-maintainer-guides/release-readiness-checklist.md)

### Testers
1. [Testing reference](03-technical-reference/testing-reference.md)
2. [Test suite tour](06-maintainer-guides/test-suite-tour.md)
3. [Example tests for a new command](05-examples/example-tests-for-new-command.md)

### Future documenters
1. [How to use these docs](00-start-here/how-to-use-these-docs.md)
2. [Documentation maintenance](06-maintainer-guides/documentation-maintenance.md)
3. [Coding LLM session primer](06-maintainer-guides/coding-llm-session-primer.md)
4. [Documentation study report](06-maintainer-guides/documentation-study-report.md)
5. [Documentation audit report](06-maintainer-guides/documentation-audit-report.md)
6. [Master index](07-reference/master-index.md)
7. [Component inventory](07-reference/component-inventory.md)
8. [Decision records index](07-reference/decision-records-index.md)

## Documentation conventions

- **Current behavior** means the behavior is implemented in source code or covered by tests.
- **Future consideration** means the idea is not implemented in this repository today.
- Technical pages cite source paths such as `../src/CommandRouter.php`, `../config/commands.php`, and `../tests/CommandRouterTest.php`.
- User-facing pages keep internals light and link to technical references for deeper details.
- Examples under [05 Examples](05-examples/README.md) are documentation snippets unless you actually add classes under `../src/Commands/` and register them in `../config/commands.php`.

## Source-of-truth policy

When code and docs disagree, update the docs to match code unless the code is being intentionally changed in the same work. For this skeleton, especially verify:

- Environment variables in `../.env.example` and `../config/bot.php`.
- Command registration in `../config/commands.php`.
- Command parser behavior in `../src/CommandRouter.php` and `../tests/CommandRouterTest.php`.
- Built-in replies in `../src/Commands/` and `../tests/BuiltInCommandsTest.php`.
- Logger behavior in `../src/ConsoleLogger.php` and `../tests/ConsoleLoggerTest.php`.

## Current limitations

Current behavior: this repository does not implement Laravel, Symfony, a database, queues, Docker-first deployment, web controllers, health/readiness endpoints, external monitoring, or hosted deployment files. It does implement lightweight daily JSON log files only.

**Future consideration:** those capabilities may be documented later only after the repository contains working code or after a guide clearly labels them as external options outside this skeleton.

## Maintenance expectations

Before changing docs, inspect the source files named in the task or in the affected page metadata. After changing docs, run the Composer checks when dependencies are available, scan for stale scaffold language, and verify that any mention of unimplemented infrastructure is marked absent or **Future consideration**.
