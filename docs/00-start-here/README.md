# Start Here Overview

**Audience:** Readers who need orientation before installing, running, or extending the bot.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../README.md`, `../../bin/bot.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`
**Related docs:** [Documentation home](../README.md), [Application at a glance](application-at-a-glance.md), [Documentation map](documentation-map.md), [From GitHub to a running bot](../01-user-guides/from-github-to-running-bot.md), [Glossary](../07-reference/glossary.md)

Start here if you are new to the repository or need a quick mental model before using deeper guides.

## What this section covers

| Page | Use it for |
| --- | --- |
| [Application at a glance](application-at-a-glance.md) | The shortest accurate explanation of what the bot does today. |
| [Documentation map](documentation-map.md) | A route to the right page by role or question. |
| [How to use these docs](how-to-use-these-docs.md) | Conventions for current behavior, future ideas, source paths, and examples. |

## First mental model

```text
CLI bootstrap -> environment/config -> validation -> logger/router/bot -> DiscordPHP event loop -> MESSAGE_CREATE -> router -> command -> reply
```

Current behavior is implemented across `../../bin/bot.php`, `../../src/Bot.php`, and `../../src/CommandRouter.php`. DiscordPHP owns the event loop; this repository supplies a small bootstrap, command router, built-in commands, and tests.

## What the project is

- A PHP 8.1+ CLI Discord bot skeleton powered by DiscordPHP.
- A prefix-command example application with built-in `ping`, `time`, `settings`, `echo`, and `help` commands.
- A framework-free base for learning and adding simple command classes.

## What the project is not

Current behavior: it is not Laravel, Symfony, a web app, a database-backed application, a queue worker, or a Docker-first stack.

**Future consideration:** any of those directions should be documented only after code exists or as clearly external guidance.

## Next step

If you want the complete first-run path from GitHub checkout to responding bot, continue to [From GitHub to a Running Bot](../01-user-guides/from-github-to-running-bot.md). If you only need the shortest command list, use [Quick start](../01-user-guides/quick-start.md). If you want to add commands, start with [Adding a command](../04-extensibility/adding-a-command.md).
