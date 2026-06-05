# Glossary

**Audience:** Readers new to Discord bots or this repository's terms.
**Status:** Current reference
**Last reviewed:** 2026-06-04
**Related files:** `../../README.md`, `../../bin/bot.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../src/CommandContext.php`, `../../config/commands.php`
**Related docs:** [Application at a glance](../00-start-here/application-at-a-glance.md), [Interaction paths](../01-user-guides/interaction-paths.md), [Using prefix commands](../01-user-guides/using-prefix-commands.md), [Architecture overview](../03-technical-reference/architecture-overview.md)

| Term | Meaning in this repository |
| --- | --- |
| Alias | Alternate command name registered in `config/commands.php`, such as `commands` for `help`. |
| Bot token | Secret Discord credential stored as `DISCORD_BOT_TOKEN`; required to connect. |
| Command context | `App\CommandContext`, the object passed into command `execute()` methods. |
| Command metadata | Usage, description, and aliases injected by `CommandRouter` for help output. |
| Discord application | Developer Portal application that owns the bot user and token. |
| DiscordPHP | The `team-reflex/discord-php` package that provides the Discord gateway client and event loop. |
| Event loop | The long-running DiscordPHP process waiting for gateway events. |
| Message Content Intent | Privileged Discord gateway intent required for message-based commands that read message text, including prefix, mention, and DM paths. |
| Prefix | Configured leading text such as `!bot` that identifies bot commands. |
| Prefix-adjacent text | Text like `!botping`; current parser intentionally ignores it. |
| Registry | The command map returned by `config/commands.php`. |
| Route content | Offline-testable router path that accepts raw message content without live Discord objects. |
| Safe reply | Generic user-facing error returned when a command throws, without implementation details. |
