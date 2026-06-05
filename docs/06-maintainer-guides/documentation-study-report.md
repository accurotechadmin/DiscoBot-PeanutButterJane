# Documentation Study Report

**Audience:** Maintainers, future documenters, and technical readers who want a synthesized view of the documentation set and the application it describes.
**Status:** Current study report
**Last reviewed:** 2026-06-05
**Related files:** `../../README.md`, `../../docs/`, `../../bin/bot.php`, `../../config/`, `../../src/`, `../../tests/`
**Related docs:** [Documentation home](../README.md), [Documentation map](../00-start-here/documentation-map.md), [Application at a glance](../00-start-here/application-at-a-glance.md), [Architecture overview](../03-technical-reference/architecture-overview.md), [Component inventory](../07-reference/component-inventory.md), [Coding LLM session primer](coding-llm-session-primer.md), [Documentation audit report](documentation-audit-report.md), [Master index](../07-reference/master-index.md)

This report summarizes the repository documentation set and the DiscordPHP bot skeleton it describes. It is a synthesized orientation document: use it when you want the big picture before reading individual user, operator, technical, extensibility, reference, and ADR pages.

The source code and tests remain the source of truth. If this report disagrees with source or tests, update this report and the more focused documentation pages together.

## Executive summary

The repository describes a lightweight, framework-free PHP CLI Discord bot starter powered by DiscordPHP. The application starts with `php bin/bot.php` or `composer bot`, loads optional `.env` values, validates configuration, creates a console logger, builds a command router, constructs a DiscordPHP client with the intents required by enabled interaction paths, listens for Discord message and slash interaction events, ignores bot/self messages for message paths, routes commands through command classes, and sends non-empty replies back to Discord.

The documentation set is organized as current-state documentation. It explains implemented behavior, avoids implying absent infrastructure, and labels unimplemented ideas as **Future consideration**. The docs are role-based, with separate paths for users, operators, command authors, maintainers, testers, and future documenters.

## Documentation corpus model

| Section | Primary audience | Purpose | Relationship to other sections |
| --- | --- | --- | --- |
| `../README.md` | All readers | Documentation home, section map, reading paths, conventions, limitations. | Entry point into every major documentation area. |
| `../00-start-here/` | First-time readers | Orientation, application summary, documentation map, status language. | Points users into guides, references, and ADRs. |
| `../01-user-guides/` | Users | GitHub checkout-to-running-bot path, installation, configuration, Discord invite, running, commands, troubleshooting. | Gives approachable task flows and links deeper technical pages. |
| `../02-operator-guides/` | Operators | Environment, secrets, dependencies, startup validation, logging, long-lived process operation. | Explains runtime management while keeping repository-owned vs external infrastructure clear. |
| `../03-technical-reference/` | Maintainers and technical readers | Architecture, lifecycle, routing, context, configuration, DiscordPHP, logging, errors, testing, scripts, files. | Source-aligned implementation reference for detailed behavior. |
| `../04-extensibility/` | Command authors | Command interface, registration, aliases, arguments, help metadata, safe DiscordPHP usage, tests. | Converts technical internals into command-authoring guidance. |
| `../05-examples/` | Command authors | Copyable command, config, alias, Discord-message, and test examples. | Supplements extensibility guides; examples are inactive until source and registry changes are made. |
| `../06-maintainer-guides/` | Maintainers | Repository tour, conventions, coding LLM primer, docs maintenance, reports, test suite, release readiness. | Keeps code, tests, docs, and decision records synchronized. |
| `../07-reference/` | All readers needing lookup | Master index, component inventory, class index, command index, environment variable index, data-flow index, ADR index, glossary. | Dense lookup layer that points back to detailed guides. |
| `../08-architecture-decisions/` | Maintainers | Accepted ADRs for design decisions and tradeoffs. | Explains why the current lightweight design exists. |
| `../../docs-completion-prompt.txt` | Documentation maintainers | Maintenance prompt and acceptance criteria for documentation cleanup. | Operational helper, not a normal reader-facing guide. |

## Application identity

### What the application is

The application is a compact Discord bot starter that runs as a PHP command-line process. It connects to Discord through DiscordPHP, listens for enabled message and slash interaction paths, and replies according to the configured prefix, mention, DM, or slash path.

The intended user experience is small and direct:

1. Install Composer dependencies.
2. Create a local `.env` or provide environment variables externally.
3. Enable Discord Message Content Intent for message-based paths.
4. Invite the bot to a Discord server.
5. Run `php bin/bot.php` or `composer bot`.
6. Type a prefix command such as `!bot ping`, or enable slash, mention, or DM paths and invoke the same registered commands through those paths.

### What the application is not

The docs consistently state that the current repository does not implement:

- Laravel or Symfony application shells;
- framework service containers;
- databases or persistence layers;
- queues or background job infrastructure;
- Docker-first deployment files;
- hosted-platform manifests;
- web controllers;
- log aggregation and monitoring stacks;
- external monitoring integrations.

These omissions are deliberate. The ADRs preserve the lightweight design boundary, and maintainer docs require future infrastructure to be added with source changes, tests or verification guidance, and updated documentation.

## Runtime component breakdown

### 1. Bootstrap component: `bin/bot.php`

`bin/bot.php` is the process entrypoint. It is responsible for:

- checking that Composer dependencies exist;
- requiring Composer autoloading;
- loading optional `.env` values without overwriting existing process variables;
- requiring `config/bot.php` and `config/commands.php`;
- validating bot configuration;
- constructing the logger, router, and bot objects;
- starting the long-running DiscordPHP event loop.

It is not responsible for prefix parsing or command behavior after `App\Bot::run()` hands control to DiscordPHP.

### 2. Configuration component: `config/bot.php`

`config/bot.php` converts environment variables into the effective bot configuration. The supported variables are:

| Variable | Config role | Default or requirement | Runtime use |
| --- | --- | --- | --- |
| `DISCORD_BOT_TOKEN` | Discord credential | Required; no usable default. | Passed to DiscordPHP. |
| `BOT_PREFIX` | Prefix command trigger | Defaults to `!bot`. | Parser, usage text, help, settings. |
| `BOT_TIMEZONE` | Timezone label | Defaults to `America/Toronto`. | `time` command and settings output. |
| `APP_ENV` | Public environment label | Defaults to `local`. | Startup log and settings output. |
| `LOG_LEVEL` | Minimum logger severity | Defaults to `debug`. | Console log filtering. |
| `BOT_ENABLE_PREFIX_COMMANDS` | Prefix path toggle | Defaults to `true`. | Enables prefixed message commands. |
| `BOT_ENABLE_SLASH_COMMANDS` | Slash path toggle | Defaults to `false`. | Enables slash command registration and ephemeral replies. |
| `BOT_ENABLE_MENTION_COMMANDS` | Mention path toggle | Defaults to `false`. | Enables public bot-mention commands. |
| `BOT_ENABLE_DM_COMMANDS` | DM path toggle | Defaults to `false`. | Enables unprefixed DM commands and the DM intent. |

The precedence model is: process environment first, optional `.env` values second, source defaults third, and startup validation last.

### 3. Command registry component: `config/commands.php`

`config/commands.php` is the canonical command registration file. It maps command names to command classes or registry arrays with class, alias, and optional per-command slash option metadata. Current built-in commands are `ping`, `time`, `settings`, `echo`, and `help`; `commands` is an alias for `help`.

The registry is consumed by `CommandRouter`, and help output is derived from the command objects and alias metadata rather than from a separate static help file.

### 4. Validation component: `ConfigValidator`

`ConfigValidator` fails fast before DiscordPHP connects. It validates:

- nonblank `DISCORD_BOT_TOKEN`;
- non-empty `BOT_PREFIX` without leading/trailing whitespace or internal whitespace;
- valid PHP timezone identifier for `BOT_TIMEZONE`;
- valid `LOG_LEVEL` in `debug`, `info`, `warning`, or `error`;
- boolean-like interaction toggles for prefix, slash, mention, and DM paths.

Validation failures are startup failures. They are surfaced in the terminal and should not be hidden by external process restarts.

### 5. Logging component: `ConsoleLogger`

`ConsoleLogger` is a tiny invokable console logger. It writes timestamped lines to STDOUT by default and filters messages by configured severity.

The current repository has lightweight daily JSON file logging through `ConsoleLogger`; `storage/logs/.gitkeep` keeps the default directory present. Operators who need aggregation, metrics, alerts, or external retention must provide those outside this repository.

### 6. Discord integration component: `Bot`

`Bot` owns the DiscordPHP boundary. It constructs the Discord client with:

- the validated token;
- default DiscordPHP intents plus Message Content Intent when message-based paths are enabled;
- memory-conscious options such as disabled message storage, member preloading, and ban retrieval.

Its runtime responsibilities are:

- log connection and ready events;
- register `MESSAGE_CREATE` handling after `ready` only when prefix, mention, or DM paths are enabled;
- register configured slash commands and install slash listeners when slash commands are enabled;
- ignore bot-authored messages;
- ignore messages authored by the bot itself;
- pass eligible prefix, mention, DM, and slash invocations to `CommandRouter`;
- send non-empty prefix and mention replies publicly, DM replies to the one-to-one conversation, and slash replies ephemerally;
- catch and warn on reply-send failures.

### 7. Routing component: `CommandRouter`

`CommandRouter` is the command boundary between Discord messages and command classes. It handles:

- command instantiation from the registry;
- alias normalization and resolution;
- prefix parsing;
- mention parsing with bot ID prefixes;
- unprefixed DM parsing;
- shared slash command dispatch;
- per-command slash option definitions;
- bare-prefix and bare-mention routing to `help`;
- command-name normalization to lowercase;
- whitespace argument splitting;
- command metadata injection for help output;
- safe exception handling around command execution.

The router exposes production and offline entrypoints. `route()` is the production prefix path with live DiscordPHP objects. `routeContent()` supports tests and raw prefix checks, `routeMentionContent()` and `routeDirectMessageContent()` cover the other message paths, `routeCommand()` is shared by slash and parsed message commands, and `parse()` handles parser-only behavior.

### 8. Context component: `CommandContext`

`CommandContext` is the value object passed to every command. It carries:

- nullable Discord client;
- nullable Discord message;
- normalized command name;
- argument list;
- active prefix;
- runtime config plus injected command metadata.

Nullable DiscordPHP objects are intentional. They allow direct command tests and router tests without live Discord connections or complex DiscordPHP fakes. Commands that read Discord-specific state must guard `discord()` and `message()` or use `hasDiscord()` and `hasMessage()`.

### 9. Command component: `src/Commands/*`

Every command implements `CommandInterface`, which requires:

```php
public function execute(CommandContext $context): string;
public function description(): string;
public function usage(string $prefix): string;
```

Command responsibilities are deliberately narrow:

- read arguments, prefix, config, or optional Discord objects from `CommandContext`;
- return a string reply;
- provide help metadata through `description()` and `usage()`;
- avoid leaking secrets;
- prefer offline-testable logic.

## Message and command data flow

```text
Discord MESSAGE_CREATE event
    -> Bot ignores bot/self messages
    -> CommandRouter receives content, prefix, config, Discord client, and message
    -> Router trims content and checks prefix
    -> Router rejects prefix-adjacent text such as !botping
    -> Router treats a bare prefix as help
    -> Router lowercases command name and splits arguments on whitespace
    -> Router resolves aliases to canonical command names
    -> Router builds CommandContext with metadata-injected config
    -> CommandInterface::execute() returns a string
    -> Bot sends non-empty reply to the original channel
```

Important parser behaviors:

- messages outside enabled paths are ignored; prefix commands require the configured prefix, mention commands require the bot mention at the start, and DM commands use unprefixed content;
- leading and trailing content whitespace is trimmed;
- command names are case-insensitive;
- repeated spaces and tabs between arguments are collapsed;
- quoted arguments are not preserved as single arguments;
- unknown commands receive a friendly suggestion to use help;
- command exceptions receive a generic safe reply.

## Built-in command model

| Typed command | Canonical command | Responsibility |
| --- | --- | --- |
| `ping` | `ping` | Confirm the bot is online by replying `Pong!`. |
| `time` | `time` | Show current time in the configured timezone. |
| `settings` | `settings` | Show safe prefix, timezone, and environment values without exposing the token. |
| `echo` | `echo` | Echo provided arguments or return a usage hint when no text is provided. |
| `help` | `help` | List command usage, descriptions, and aliases from router-injected metadata. |
| `commands` | `help` | Alias for `help`; not a separate command class. |

## Operational relationships

### Environment and secrets

The only secret documented today is `DISCORD_BOT_TOKEN`. It belongs in `.env` for local development or externally injected environment variables for shared runtimes. It should not be committed, logged, pasted into support channels, or displayed by commands.

`APP_ENV` is safe but public because the `settings` command displays it. `BOT_PREFIX` and `BOT_TIMEZONE` are also visible in Discord output through help, usage, settings, or time replies.

### Startup and long-lived execution

Startup is fail-fast. Missing dependencies, invalid environment, blank token, invalid prefix, invalid timezone, and invalid log level should be corrected before process managers attempt restarts.

Long-lived operation is external to the repository. Supervisors or hosted runtimes can run the bot, but the repository does not provide service units, container images, hosted manifests, or restart policies.

### Logging

Logs are console output. Normal startup, ready messages, command exception warnings, reply-send warnings, and validation errors are visible through STDOUT or STDERR depending on whether the logger exists yet.

If log aggregation, metrics, or monitoring alerts are needed, they are external integrations or **Future consideration** features requiring implementation and documentation updates.

## Extensibility relationships

Command authoring flows through four coordinated artifacts:

1. A command class under `src/Commands/` implementing `CommandInterface`.
2. A registration entry in `config/commands.php`.
3. Tests, usually direct command tests and optionally router raw-content tests.
4. Documentation updates in user guides, extensibility pages, examples, and reference indexes if the command is a real registered command.

The docs distinguish full-file examples from snippets. Examples remain documentation-only until source and registry changes make them active.

## Testing relationships

The test suite is offline and protects current behavior without Discord network calls.

| Test area | Protects |
| --- | --- |
| Built-in command tests | Reply shapes, command metadata, `time`, `settings`, `echo`, and help behavior. |
| Router tests | Prefix parsing, aliases, metadata injection, unknown commands, registry behavior, and safe exception handling. |
| Config validator tests | Startup validation rules. |
| Console logger tests | Severity filtering and invalid configured log levels. |

The testing model depends on nullable DiscordPHP objects in `CommandContext` and the router's `routeContent()` method.

## Architecture decisions in context

| ADR | Decision | Main consequence |
| --- | --- | --- |
| ADR 0001 | Keep a lightweight CLI DiscordPHP skeleton. | Small, inspectable runtime; no default framework, database, queue, Docker, or controller layer. |
| ADR 0002 | Keep prefix command routing as the default. | Simple offline-testable command UX; requires Discord Message Content Intent for message content. |
| ADR 0005 | Add configurable interaction paths. | Prefix remains default; slash, mention, and DM paths are opt-in with path-specific visibility. |
| ADR 0003 | Allow nullable DiscordPHP objects in `CommandContext`. | Easier unit tests; command authors must null-guard Discord object access. |
| ADR 0004 | Use lightweight console and daily JSON logging. | Lightweight visible output with basic generated JSON files and no logging framework dependency. |

## Maintenance implications

When behavior changes, update documentation as a set rather than one page at a time:

- Config changes affect `.env.example`, user configuration docs, operator environment docs, technical configuration reference, and environment-variable index.
- Command changes affect built-in command docs, command index, examples, tests, and possibly help metadata docs.
- Parser changes affect user prefix docs, command routing reference, data-flow index, ADR 0002 if the decision changes, and router tests.
- Logger changes affect operator logging docs, technical logging reference, ADR 0004 if the decision changes, and logger tests.
- Command interface or context changes affect extensibility docs, examples, tests, command context reference, and ADR 0003 if the decision changes.
- Runtime/deployment changes must keep repository-owned implementation distinct from external process-management advice.

## Key takeaways

1. The bot is intentionally small: one PHP CLI process, DiscordPHP event loop, explicit config, explicit command registry, and string-returning commands.
2. Prefix commands remain the default command UX; slash, mention, and DM paths are configurable, and Message Content Intent is required for message-content paths.
3. The runtime pipeline is easy to trace from `bin/bot.php` to `Bot`, `CommandRouter`, `CommandContext`, and command classes.
4. Configuration is environment-backed and validated before Discord connects.
5. Logging is console plus optional daily JSON files; aggregation, metrics, and alerting are external.
6. Tests avoid live Discord calls through nullable context objects and raw-content routing.
7. Examples are documentation assets, not registered commands.
8. The documentation set is role-oriented, source-aligned, and intentionally careful about absent infrastructure.
