# Coding LLM Session Primer

**Audience:** Coding LLM sessions, maintainers prompting coding agents, and future documenters who need a fast but thorough orientation path.
**Status:** Current primer prompt with efficacy notes
**Last reviewed:** 2026-06-04
**Related files:** `../../README.md`, `../../docs/`, `../../bin/bot.php`, `../../config/`, `../../src/`, `../../tests/`, `../../composer.json`
**Related docs:** [Documentation home](../README.md), [Documentation study report](documentation-study-report.md), [Repository tour](repository-tour.md), [Architecture overview](../03-technical-reference/architecture-overview.md), [Master index](../07-reference/master-index.md)

Use this page to initialize a new coding LLM session before asking it to change the repository. It is written as both a primer and a copyable prompt: it maps the application and documentation set, gives an adaptive reading sequence, and tells the LLM how to report its understanding before editing code or docs.

## Prompt efficacy evaluation

The previous session prompt was accurate and useful: it named the lightweight DiscordPHP CLI architecture, listed the current built-in commands, emphasized source and tests as the source of truth, and steered agents away from inventing absent infrastructure. The application and documentation set support those expectations well: the source is compact, the tests run offline, and the docs are organized into start-here pages, user/operator guides, technical references, extensibility examples, maintainer guides, indexes, and ADRs.

A more robust prompt should improve four areas:

1. **Instruction hierarchy and repository hygiene.** Ask the agent to inspect local instructions, current branch state, and uncommitted changes before reading broadly or editing. This reduces accidental overwrites and aligns the session with the operator environment.
2. **Adaptive reading.** Keep the full orientation path available, but make the required reading task-sensitive. Reading every page before a small change can waste context; skipping the technical reference before a routing, config, logging, or documentation change can cause drift.
3. **Verification against source.** Require the agent to confirm relevant docs against `bin/`, `config/`, `src/`, `tests/`, `.env.example`, and `composer.json` instead of trusting summaries alone.
4. **Change synchronization and checks.** Make the expected edit loop explicit: plan, make scoped changes, update indexes and examples when needed, run the most relevant Composer or documentation checks, and report limitations without overstating unimplemented capabilities.

## Improved copyable session prompt

```text
You are working in the DiscordPHP Bot Skeleton repository.

Start by orienting yourself before editing. Treat source files, tests, Composer scripts, and committed repository files as the source of truth. Documentation describes current behavior only; if a feature is absent from the repository, do not imply it exists. Ideas not implemented today must be labeled exactly as **Future consideration**.

Repository and instruction hygiene:
- Inspect local agent or maintainer instructions that apply to the files you may touch.
- Check the working tree before editing and do not overwrite user changes.
- Prefer `rg` for repository search; avoid recursive grep.
- Use existing Composer scripts and documented checks when dependencies are available.
- Do not add a framework, database, queue, web controller, Docker-first workflow, retained file logging, monitoring stack, or process manager unless the requested task explicitly implements and documents it.

Application summary to verify against source:
- This is a lightweight PHP 8.1+ CLI Discord bot skeleton powered by DiscordPHP.
- The bot starts with `php bin/bot.php` or `composer bot`.
- `bin/bot.php` loads Composer, optional `.env` values, bot config, command config, startup validation, the console/JSON logger, command router, lifecycle wrapper, rate limiter, and Discord bot runtime.
- `config/bot.php` reads `DISCORD_BOT_TOKEN`, `BOT_PREFIX`, `BOT_TIMEZONE`, `APP_ENV`, `LOG_LEVEL`, and the interaction-path toggles.
- `config/commands.php` is the command registry and alias/slash-option source.
- `src/Bot.php` owns DiscordPHP setup, conditional intents, ready/message/slash listeners, bot/self-message guards, public message replies, DM replies, and ephemeral slash replies.
- `src/CommandRouter.php` owns prefix parsing, mention parsing, DM parsing, slash dispatch, aliases, command metadata, slash command definitions, and safe command exception replies.
- `src/CommandContext.php` carries nullable DiscordPHP objects, command name, arguments, prefix, and config into commands.
- Commands live in `src/Commands/` and implement `CommandInterface` with `execute()`, `description()`, and `usage()`.
- Current built-in commands are `ping`, `time`, `settings`, `echo`, and `help`; `commands` is an alias for `help`.
- Logging uses `ConsoleLogger` for console output and optional daily structured JSON files; `storage/logs/.gitkeep` keeps the default generated-log directory present.

Adaptive reading path:
1. Always read `README.md`, `docs/README.md`, `docs/00-start-here/application-at-a-glance.md`, and `docs/00-start-here/how-to-use-these-docs.md` for project identity, vocabulary, current-behavior rules, and documentation conventions.
2. For any code change, read `docs/06-maintainer-guides/repository-tour.md`, `docs/03-technical-reference/architecture-overview.md`, and the source/test files directly involved in the task.
3. For runtime, Discord client, interaction-path, or lifecycle changes, read `docs/03-technical-reference/runtime-lifecycle.md`, `docs/03-technical-reference/discordphp-integration.md`, `docs/03-technical-reference/interaction-paths-reference.md`, and `docs/08-architecture-decisions/adr-0005-configurable-interaction-paths.md`.
4. For command parsing, aliases, help output, command metadata, or command registration, read `docs/03-technical-reference/command-routing-reference.md`, `docs/03-technical-reference/command-context-reference.md`, `docs/04-extensibility/adding-a-command.md`, `docs/04-extensibility/command-interface-contract.md`, `docs/04-extensibility/command-registration-and-aliases.md`, `docs/04-extensibility/command-arguments-and-context.md`, `docs/04-extensibility/command-help-metadata.md`, and relevant examples under `docs/05-examples/`.
5. For configuration, validation, environment variables, or startup failures, read `docs/03-technical-reference/configuration-reference.md`, `docs/02-operator-guides/environment-management.md`, `docs/02-operator-guides/startup-validation.md`, `docs/07-reference/environment-variable-index.md`, `.env.example`, `config/bot.php`, and `src/ConfigValidator.php`.
6. For logging or operations, read `docs/03-technical-reference/logging-reference.md`, `docs/02-operator-guides/logging-and-log-levels.md`, `docs/02-operator-guides/running-in-long-lived-sessions.md`, and `docs/08-architecture-decisions/adr-0004-console-only-logging.md`.
7. For tests or behavior safety, read `docs/03-technical-reference/testing-reference.md`, `docs/06-maintainer-guides/test-suite-tour.md`, and the relevant files under `tests/`.
8. For documentation changes, read `docs/06-maintainer-guides/documentation-maintenance.md`, `docs/06-maintainer-guides/documentation-study-report.md`, `docs/06-maintainer-guides/documentation-audit-report.md`, and any affected section README or reference index.
9. For architectural changes or changes touching lightweight boundaries, read `docs/08-architecture-decisions/README.md` and the ADRs relevant to the touched area before finalizing the design.
10. Use `docs/07-reference/master-index.md`, `docs/07-reference/class-index.md`, `docs/07-reference/command-index.md`, `docs/07-reference/environment-variable-index.md`, and `docs/07-reference/data-flow-index.md` as lookup tables to confirm exact names and relationships.

Before making non-trivial edits, produce a concise high-level orientation report that includes:
- the runtime flow in one paragraph;
- the key source files and their responsibilities;
- the documentation sections most relevant to the requested task;
- current boundaries or unimplemented features that must not be overstated;
- the exact source, test, and doc files likely to be touched;
- the tests or checks likely needed for the task.

When changing the repository:
- Keep the change scoped to the request.
- Update source, tests, docs, examples, references, and ADRs together when behavior changes.
- Update all section READMEs, indexes, maps, or reports that enumerate any new, renamed, or removed document, command, class, environment variable, data flow, or architecture decision.
- Keep command examples synchronized with `CommandInterface`, `CommandContext`, `CommandUsage`, and `config/commands.php`.
- Keep environment documentation synchronized with `.env.example`, `config/bot.php`, and `ConfigValidator`.
- Keep testing documentation synchronized with `phpunit.xml`, `composer.json`, and `tests/`.
- Prefer small, source-aligned examples over speculative patterns.
- Run relevant checks such as `composer lint`, `composer test`, `composer check`, and any documented Markdown/link validation command; if dependencies or tools are unavailable, report that limitation clearly.
```

## Why this version is stronger

This version keeps the broad-to-specific orientation path, but it makes the session more practical by separating always-read documents from task-specific documents. It also explicitly tells the agent to verify summaries against source, preserve user work, keep indexes synchronized, and choose checks based on the changed area. Those additions make the prompt better suited for small fixes, larger feature work, and documentation maintenance without weakening the repository's current-behavior boundary.

## Concise high-level report to expect from a prepared session

A prepared coding LLM session should discover and report the following before significant edits:

- The project is a framework-free PHP CLI Discord bot skeleton powered by DiscordPHP, started through `php bin/bot.php` or `composer bot`.
- The runtime path is environment/config loading, startup validation, console logger setup, command router setup, DiscordPHP client creation, event listener registration, message or interaction handling, command routing, command execution, and Discord reply sending.
- The core source files are `bin/bot.php`, `config/bot.php`, `config/commands.php`, `src/Bot.php`, `src/CommandRouter.php`, `src/CommandContext.php`, `src/ConfigValidator.php`, `src/ConsoleLogger.php`, `src/ParsedCommand.php`, and `src/Commands/*`.
- Prefix commands remain enabled by default; slash, mention, and DM command paths are opt-in through `.env` settings. Message Content Intent is required for message-content paths.
- Current built-in commands are `ping`, `time`, `settings`, `echo`, `help`, plus the `commands` alias for `help`.
- Command classes implement `CommandInterface`, return strings from `execute()`, expose `description()` and `usage()`, and receive arguments, prefix, config, and nullable DiscordPHP objects through `CommandContext`.
- Logging is console plus optional daily structured JSON files; monitoring, containers, process managers, databases, queues, framework services, and web controllers are external or absent unless future source changes add them.
- Tests are offline and center on built-in command behavior, router parsing and dispatch, config validation, and logger filtering.
- Documentation changes must be cross-linked through the appropriate README, map, maintainer guide, reference index, and audit/study documents when those pages enumerate the changed area.
