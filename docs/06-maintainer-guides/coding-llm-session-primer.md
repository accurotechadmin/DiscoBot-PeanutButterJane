# Coding LLM Session Primer

**Audience:** Coding LLM sessions, maintainers prompting coding agents, and future documenters who need a fast but thorough orientation path.
**Status:** Current primer prompt with efficacy notes
**Last reviewed:** 2026-06-05
**Related files:** `../../README.md`, `../../docs/`, `../../bin/bot.php`, `../../bin/sync-slash-commands.php`, `../../config/`, `../../src/`, `../../tests/`, `../../composer.json`, `../../phpunit.xml`, `../../.env.example`
**Related docs:** [Documentation home](../README.md), [Documentation study report](documentation-study-report.md), [Repository tour](repository-tour.md), [Architecture overview](../03-technical-reference/architecture-overview.md), [Runtime lifecycle](../03-technical-reference/runtime-lifecycle.md), [Composer scripts reference](../03-technical-reference/composer-scripts-reference.md), [Master index](../07-reference/master-index.md)

Use this page to initialize a new coding LLM session before asking it to change the repository. It is written as both a primer and a copyable prompt: it maps the application and documentation set, gives an adaptive reading sequence, and tells the LLM how to report its understanding before editing code or docs.

The best use of this page is to paste the prompt below into a fresh coding session, then append the concrete task. The agent should use this primer to build a working mental model, not as a substitute for reading source. If any summary in this page conflicts with committed code, tests, Composer scripts, `.env.example`, or current docs that are closer to the touched area, the closer source wins and the agent should call out the mismatch before editing.

## Prompt efficacy evaluation

The previous session prompt was accurate and useful: it named the lightweight DiscordPHP CLI architecture, listed the current built-in commands, emphasized source and tests as the source of truth, and steered agents away from inventing absent infrastructure. The application and documentation set support those expectations well: the source is compact, the tests run offline, and the docs are organized into start-here pages, user/operator guides, technical references, extensibility examples, maintainer guides, indexes, and ADRs.

A more robust prompt should improve four areas:

1. **Instruction hierarchy and repository hygiene.** Ask the agent to inspect local instructions, current branch state, and uncommitted changes before reading broadly or editing. This reduces accidental overwrites and aligns the session with the operator environment.
2. **Adaptive reading.** Keep the full orientation path available, but make the required reading task-sensitive. Reading every page before a small change can waste context; skipping the technical reference before a routing, config, logging, or documentation change can cause drift.
3. **Verification against source.** Require the agent to confirm relevant docs against `bin/`, `config/`, `src/`, `tests/`, `.env.example`, `composer.json`, and `phpunit.xml` instead of trusting summaries alone.
4. **Change synchronization and checks.** Make the expected edit loop explicit: plan, make scoped changes, update indexes and examples when needed, run the most relevant Composer or documentation checks, and report limitations without overstating unimplemented capabilities.

Additional current-session strengthening points:

5. **Runtime and operations coverage.** Include slash-command synchronization, runtime lifecycle, signal shutdown, structured daily JSON logs, and the basic in-memory rate limiter in the boot model so agents do not miss behavior added outside the original prefix-command skeleton.
6. **Documentation-corpus navigation.** Tell agents when to consult section READMEs, maintainer reports, reference indexes, component inventories, and ADRs, because many docs intentionally enumerate current files, commands, variables, data flows, and decisions.
7. **Task-specific playbooks.** Give fresh agents concrete read/edit/check paths for common work types: command changes, environment/config changes, interaction-path changes, logging/runtime changes, tests, docs-only changes, and dependency changes.
8. **Explicit reporting contract.** Require the agent to distinguish verified facts, inferred risks, planned touch points, checks run, and any environment limitations.

## Improved copyable session prompt

```text
You are working in the DiscordPHP Bot Skeleton repository.

Start by orienting yourself before editing. Treat source files, tests, Composer scripts, `.env.example`, `phpunit.xml`, and committed repository files as the source of truth. Documentation describes current behavior only; if a feature is absent from the repository, do not imply it exists. Ideas not implemented today must be labeled exactly as **Future consideration**.

Repository and instruction hygiene:
- Inspect local agent or maintainer instructions that apply to the files you may touch. Respect the closest applicable instructions while treating direct user and system instructions as higher priority.
- Check the current branch and working tree before editing. Do not overwrite user changes, generated credentials, local `.env` values, or unrelated work.
- Prefer `rg` for repository search; avoid recursive grep. Use targeted reads of nearby files instead of blindly loading the entire repository into context.
- Note whether `vendor/` exists before choosing Composer checks. If dependencies are missing, report that clearly and do not claim tests passed.
- Use existing Composer scripts and documented checks when dependencies are available.
- Do not add a framework, database, queue, web controller, Docker-first workflow, external log aggregation, monitoring stack, external process manager, or hosted deployment story unless the requested task explicitly implements and documents it.
- Keep the skeleton framework-free, CLI-first, and DiscordPHP-centered unless a deliberate architecture change is requested and documented.

Application summary to verify against source:
- This is a lightweight PHP 8.1+ CLI Discord bot skeleton powered by DiscordPHP.
- The bot starts with `php bin/bot.php` or `composer bot`.
- Slash command definition synchronization is a separate explicit operation: `php bin/sync-slash-commands.php` or `composer sync-slash-commands`. Normal bot startup does not write slash command definitions to Discord.
- `bin/bot.php` loads Composer, optional `.env` values, bot config, command config, startup validation, the console/JSON logger, command router, runtime lifecycle wrapper, rate limiter, and Discord bot runtime.
- `bin/sync-slash-commands.php` loads the same environment/config foundation, validates startup settings, builds router slash definitions, and uses `SlashCommandSynchronizer` for Discord registration.
- `config/bot.php` reads `DISCORD_BOT_TOKEN`, `BOT_PREFIX`, `BOT_TIMEZONE`, `APP_ENV`, `LOG_LEVEL`, `LOG_FILE_ENABLED`, `LOG_FILE_DIR`, `BOT_ENABLE_PREFIX_COMMANDS`, `BOT_ENABLE_SLASH_COMMANDS`, `BOT_ENABLE_MENTION_COMMANDS`, `BOT_ENABLE_DM_COMMANDS`, `BOT_RATE_LIMIT_MAX_ATTEMPTS`, and `BOT_RATE_LIMIT_WINDOW_SECONDS`.
- `config/commands.php` is the command registry and alias/slash-option source.
- `src/Bot.php` owns DiscordPHP setup, conditional intents, ready/message/slash listeners, bot/self-message guards, public message replies, DM replies, ephemeral slash replies, signal-aware shutdown integration, and per-user rate-limit checks before dispatch.
- `src/CommandRouter.php` owns prefix parsing, mention parsing, DM parsing, slash dispatch, aliases, command metadata, slash command definitions, and safe command exception replies.
- `src/CommandContext.php` carries nullable DiscordPHP objects, command name, arguments, prefix, and config into commands.
- `src/ConfigValidator.php` validates startup config, environment-derived booleans/integers, logging settings, interaction-path toggles, command registry entries, aliases, and slash option metadata.
- `src/ConsoleLogger.php` writes filtered console lines and, when enabled, daily structured JSON log records.
- `src/RateLimiter.php` implements a basic in-memory per-user command rate limiter; `0` max attempts disables limiting.
- `src/RuntimeLifecycle.php` handles shutdown state, shutdown logging, and optional SIGINT/SIGTERM handler installation when `pcntl` is available.
- `src/SlashCommandSynchronizer.php` connects to DiscordPHP briefly and synchronizes router-built slash command definitions outside normal runtime startup.
- Commands live in `src/Commands/` and implement `CommandInterface` with `execute()`, `description()`, and `usage()`.
- Current built-in commands are `ping`, `time`, `settings`, `echo`, and `help`; `commands` is an alias for `help`.
- Logging uses `ConsoleLogger` for console output and optional daily structured JSON files; `storage/logs/.gitkeep` keeps the default generated-log directory present.
- Tests are offline and use nullable DiscordPHP objects or raw content where possible. They should not connect to Discord unless a future task deliberately adds integration tests and documents them.

Documentation corpus map:
- `README.md` and `docs/README.md` define the project identity, section map, and recommended reading paths.
- `docs/00-start-here/` defines the first mental model, current-behavior language, and documentation conventions.
- `docs/01-user-guides/` explains installation, running, configuration, command usage, interaction paths, inviting the bot, troubleshooting, and common questions from an operator/user perspective.
- `docs/02-operator-guides/` covers dependency management, environment management, startup validation, logging, long-lived sessions, and secrets.
- `docs/03-technical-reference/` is the main technical source for architecture, runtime lifecycle, DiscordPHP integration, command routing/context, configuration, interaction paths, logging, testing, errors, Composer scripts, and file layout.
- `docs/04-extensibility/` and `docs/05-examples/` are the command-author guides and copyable examples. Keep them synchronized with `CommandInterface`, `CommandContext`, `CommandUsage`, and `config/commands.php`.
- `docs/06-maintainer-guides/` contains repository tours, test-suite tours, code/documentation style, audit/study reports, release readiness, and this primer.
- `docs/07-reference/` contains lookup indexes for classes, commands, environment variables, data flows, components, decisions, glossary terms, and the master index.
- `docs/08-architecture-decisions/` records the lightweight CLI, routing, nullable-context, logging, and configurable-interaction-path decisions. Update or add ADRs only for meaningful architecture changes.

Adaptive reading path:
1. Always read `README.md`, `docs/README.md`, `docs/00-start-here/application-at-a-glance.md`, and `docs/00-start-here/how-to-use-these-docs.md` for project identity, vocabulary, current-behavior rules, and documentation conventions.
2. For any code change, read `docs/06-maintainer-guides/repository-tour.md`, `docs/06-maintainer-guides/code-style-and-conventions.md`, `docs/03-technical-reference/architecture-overview.md`, `docs/03-technical-reference/file-and-directory-reference.md`, and the source/test files directly involved in the task.
3. For runtime, Discord client, interaction-path, slash synchronization, intents, or lifecycle changes, read `docs/03-technical-reference/runtime-lifecycle.md`, `docs/03-technical-reference/discordphp-integration.md`, `docs/03-technical-reference/interaction-paths-reference.md`, `docs/08-architecture-decisions/adr-0005-configurable-interaction-paths.md`, `src/Bot.php`, `src/RuntimeLifecycle.php`, `src/SlashCommandSynchronizer.php`, `bin/bot.php`, and `bin/sync-slash-commands.php`.
4. For command parsing, aliases, help output, command metadata, slash options, command registration, or command behavior, read `docs/03-technical-reference/command-routing-reference.md`, `docs/03-technical-reference/command-context-reference.md`, `docs/04-extensibility/adding-a-command.md`, `docs/04-extensibility/command-interface-contract.md`, `docs/04-extensibility/command-registration-and-aliases.md`, `docs/04-extensibility/command-arguments-and-context.md`, `docs/04-extensibility/command-help-metadata.md`, `docs/04-extensibility/testing-new-commands.md`, relevant examples under `docs/05-examples/`, `config/commands.php`, `src/CommandRouter.php`, `src/CommandContext.php`, `src/Commands/`, `tests/BuiltInCommandsTest.php`, and `tests/CommandRouterTest.php`.
5. For configuration, validation, environment variables, startup failures, or `.env.example`, read `docs/03-technical-reference/configuration-reference.md`, `docs/02-operator-guides/environment-management.md`, `docs/02-operator-guides/startup-validation.md`, `docs/07-reference/environment-variable-index.md`, `.env.example`, `config/bot.php`, `src/ConfigValidator.php`, and `tests/ConfigValidatorTest.php`.
6. For logging, operations, JSON log files, long-lived execution, shutdown, or rate limiting, read `docs/03-technical-reference/logging-reference.md`, `docs/03-technical-reference/runtime-lifecycle.md`, `docs/02-operator-guides/logging-and-log-levels.md`, `docs/02-operator-guides/running-in-long-lived-sessions.md`, `docs/08-architecture-decisions/adr-0004-console-only-logging.md`, `src/ConsoleLogger.php`, `src/RateLimiter.php`, `src/RuntimeLifecycle.php`, `tests/ConsoleLoggerTest.php`, `tests/RateLimiterTest.php`, and `tests/RuntimeLifecycleTest.php`.
7. For tests or behavior safety, read `docs/03-technical-reference/testing-reference.md`, `docs/06-maintainer-guides/test-suite-tour.md`, `docs/04-extensibility/testing-new-commands.md`, `phpunit.xml`, `composer.json`, and the relevant files under `tests/`.
8. For documentation changes, read `docs/06-maintainer-guides/documentation-maintenance.md`, `docs/06-maintainer-guides/documentation-study-report.md`, `docs/06-maintainer-guides/documentation-audit-report.md`, the affected section README, and any reference index that enumerates the changed concept.
9. For dependency, Composer script, PHP version, PHPUnit, or package changes, read `composer.json`, `docs/03-technical-reference/composer-scripts-reference.md`, `docs/02-operator-guides/dependency-management.md`, `docs/01-user-guides/installation.md`, and `docs/03-technical-reference/testing-reference.md`.
10. For architectural changes or changes touching lightweight boundaries, read `docs/08-architecture-decisions/README.md` and the ADRs relevant to the touched area before finalizing the design.
11. Use `docs/07-reference/master-index.md`, `docs/07-reference/class-index.md`, `docs/07-reference/command-index.md`, `docs/07-reference/environment-variable-index.md`, `docs/07-reference/data-flow-index.md`, `docs/07-reference/component-inventory.md`, `docs/07-reference/decision-records-index.md`, and `docs/07-reference/glossary.md` as lookup tables to confirm exact names and relationships.

Source-of-truth cross-check matrix:
- Runtime entrypoints: compare `README.md`, runtime docs, `composer.json`, `bin/bot.php`, and `bin/sync-slash-commands.php`.
- Environment variables: compare `.env.example`, `config/bot.php`, `src/ConfigValidator.php`, environment docs, and the environment-variable index.
- Commands: compare `config/commands.php`, `src/Commands/`, command docs, examples, command index, and built-in/router tests.
- Interaction paths: compare `config/bot.php`, `src/Bot.php`, `src/CommandRouter.php`, interaction-path docs, runtime docs, ADR 0005, and router tests.
- Logging and shutdown: compare `src/ConsoleLogger.php`, `src/RuntimeLifecycle.php`, logging/runtime/operator docs, ADR 0004, and matching tests.
- Tests/checks: compare `composer.json`, `phpunit.xml`, testing docs, test-suite tour, and actual `tests/` files.
- Architecture boundaries: compare README, architecture overview, repository tour, component inventory, and ADRs.

Task playbooks:
- Small docs-only clarification: read the always-read docs, the target page, its related docs, and any affected index; verify claims against source; update metadata only if the review date/status truly changed; run a lightweight documentation/source consistency check if available, otherwise at least inspect links/paths with `rg`.
- New or changed command: update the command class, registry, command tests, router tests when parsing/aliases/metadata are involved, user command docs, extensibility examples if patterns change, command index, class/component indexes when applicable, and slash synchronization notes if slash metadata changes.
- Environment/config change: update `.env.example`, `config/bot.php`, `ConfigValidator`, config tests, user/operator config docs, startup validation docs, environment-variable index, and any sample `.env` blocks.
- Interaction path or Discord event change: update `Bot`, `CommandRouter`, lifecycle/synchronizer code as needed, router/runtime tests, user interaction docs, technical references, data-flow index, and ADRs if the decision boundary changes.
- Logging/rate-limit/runtime lifecycle change: update the relevant source, tests, operator guides, technical references, config/env docs if variables changed, and ADRs when the logging or supervision model changes.
- Dependency or script change: update `composer.json`, lock-file expectations if introduced later, Composer script reference, installation/dependency docs, testing reference, and CI/release checklists if they exist or are added.
- ADR-worthy design change: first summarize the current ADR boundary, then either update the existing ADR for an evolution of the same decision or add a new numbered ADR for a new decision. Cross-link it from `docs/08-architecture-decisions/README.md` and `docs/07-reference/decision-records-index.md`.

Before making non-trivial edits, produce a concise high-level orientation report that includes:
- the runtime flow in one paragraph;
- the key source files and their responsibilities;
- the documentation sections most relevant to the requested task;
- verified current behavior and any inferred risks, clearly separated;
- current boundaries or unimplemented features that must not be overstated;
- the exact source, test, and doc files likely to be touched;
- the tests or checks likely needed for the task.

When changing the repository:
- Keep the change scoped to the request.
- Update source, tests, docs, examples, references, and ADRs together when behavior changes.
- Update all section READMEs, indexes, maps, inventories, or reports that enumerate any new, renamed, or removed document, command, class, environment variable, data flow, Composer script, generated file, or architecture decision.
- Keep command examples synchronized with `CommandInterface`, `CommandContext`, `CommandUsage`, and `config/commands.php`.
- Keep environment documentation synchronized with `.env.example`, `config/bot.php`, and `ConfigValidator`.
- Keep testing documentation synchronized with `phpunit.xml`, `composer.json`, and `tests/`.
- Keep runtime and operation documentation synchronized with `bin/`, `src/Bot.php`, `src/RuntimeLifecycle.php`, `src/SlashCommandSynchronizer.php`, `src/RateLimiter.php`, and relevant operator guides.
- Prefer small, source-aligned examples over speculative patterns.
- Preserve existing documentation richness. Expand or correct; do not flatten useful context into terse notes.
- Run relevant checks such as `composer lint`, `composer test`, `composer check`, and any documented Markdown/link validation command; if dependencies or tools are unavailable, report that limitation clearly.

Common pitfalls to avoid:
- Do not treat slash commands as automatically registered during `composer bot`; use the explicit synchronization entrypoint.
- Do not say file logging is absent; current behavior includes optional daily structured JSON log files.
- Do not say mention or DM commands are unimplemented; they are implemented opt-in paths.
- Do not forget the rate limiter when describing dispatch or runtime behavior.
- Do not document secrets, token values, or local `.env` contents.
- Do not imply live Discord integration tests exist in the current suite.
- Do not add broad abstractions just because a pattern could scale later; mark such ideas as **Future consideration** unless implemented now.
```

## Why this version is stronger

This version keeps the broad-to-specific orientation path, but it makes the session more practical by separating always-read documents from task-specific documents. It also explicitly tells the agent to verify summaries against source, preserve user work, keep indexes synchronized, and choose checks based on the changed area. Those additions make the prompt better suited for small fixes, larger feature work, and documentation maintenance without weakening the repository's current-behavior boundary.

The expanded prompt also makes the current runtime harder to misstate. A fresh session is now primed to notice that slash command synchronization is separate from normal startup, that logging includes optional daily JSON files, that prefix/mention/DM paths depend on Message Content Intent, that rate limiting is part of dispatch safety, and that shutdown handling is lightweight and signal-aware when `pcntl` is present. Those details are easy to miss if the agent only remembers the earliest prefix-command skeleton.

Finally, the prompt now gives the agent a source-of-truth matrix and common task playbooks. That turns the documentation set from a long reading list into an operational checklist: each likely change type points to the code, tests, docs, indexes, examples, and ADRs that must stay synchronized.

## Concise high-level report to expect from a prepared session

A prepared coding LLM session should discover and report the following before significant edits:

- The project is a framework-free PHP CLI Discord bot skeleton powered by DiscordPHP, started through `php bin/bot.php` or `composer bot`.
- Slash command definitions are synchronized separately through `php bin/sync-slash-commands.php` or `composer sync-slash-commands`; normal runtime startup only installs slash listeners when slash commands are enabled.
- The runtime path is environment/config loading, startup validation, console/JSON logger setup, command router setup, runtime lifecycle setup, basic rate limiter setup, DiscordPHP client creation, event listener registration, message or interaction handling, command routing, command execution, and Discord reply sending.
- The core source files are `bin/bot.php`, `bin/sync-slash-commands.php`, `config/bot.php`, `config/commands.php`, `src/Bot.php`, `src/CommandRouter.php`, `src/CommandContext.php`, `src/ConfigValidator.php`, `src/ConsoleLogger.php`, `src/ParsedCommand.php`, `src/RateLimiter.php`, `src/RuntimeLifecycle.php`, `src/SlashCommandSynchronizer.php`, and `src/Commands/*`.
- Prefix commands remain enabled by default; slash, mention, and DM command paths are opt-in through `.env` settings. Message Content Intent is required for message-content paths, and Direct Messages intent is added when DM commands are enabled.
- Current built-in commands are `ping`, `time`, `settings`, `echo`, `help`, plus the `commands` alias for `help`.
- Command classes implement `CommandInterface`, return strings from `execute()`, expose `description()` and `usage()`, and receive arguments, prefix, config, and nullable DiscordPHP objects through `CommandContext`.
- Logging is console plus optional daily structured JSON files; monitoring, containers, process managers, databases, queues, framework services, and web controllers are external or absent unless future source changes add them.
- Shutdown behavior is limited to lightweight lifecycle state, a shutdown logger, and SIGINT/SIGTERM handling when `pcntl` is available; reconnection/backoff, health checks, and readiness endpoints are not implemented.
- Tests are offline and center on built-in command behavior, router parsing and dispatch, config validation, logger filtering/file output, rate limiting, and runtime lifecycle state.
- Documentation changes must be cross-linked through the appropriate README, map, maintainer guide, reference index, component inventory, decision-record index, audit/study document, and ADR when those pages enumerate the changed area.

## Quick boot checklist for a fresh coding session

Use this short checklist after pasting the full prompt and before starting work:

1. Read applicable local instructions and check `git status --short`.
2. Read the always-read docs and identify the task-specific reading path.
3. Verify the relevant claims against source, tests, Composer scripts, and `.env.example`.
4. Produce the orientation report for non-trivial changes.
5. Make the smallest coherent source/doc/test changes.
6. Update indexes, examples, READMEs, reports, and ADRs that enumerate the changed area.
7. Run the relevant checks, or report exactly why they could not run.
8. Summarize changed files, checks, and any remaining limitations without overstating unimplemented features.
