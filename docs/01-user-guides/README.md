# User Guides

**Audience:** People installing, configuring, inviting, running, and using the bot.
**Status:** Current guide
**Last reviewed:** 2026-06-05
**Related files:** `../../README.md`, `../../.env.example`, `../../bin/bot.php`, `../../config/bot.php`, `../../config/commands.php`
**Related docs:** [Authoritative installation, startup, and configuration guide](installation-startup-configuration-guide.md), [From GitHub to a running bot](from-github-to-running-bot.md), [Quick start](quick-start.md), [Troubleshooting](troubleshooting.md), [Technical reference](../03-technical-reference/README.md)

These guides keep implementation detail light and focus on getting a working Discord bot into a server or DM, with prefix commands enabled by default and optional slash, mention, and DM paths.

## Pages

| Page | Use it for |
| --- | --- |
| [Authoritative installation, startup, and configuration guide](installation-startup-configuration-guide.md) | Single consolidated source for local, staging, and production-server installation, startup, configuration, and operation within current repository boundaries. |
| [From GitHub to a running bot](from-github-to-running-bot.md) | Complete path from fresh checkout to responding bot that is ready to extend. |
| [Installation](installation.md) | Install Composer dependencies and understand requirements. |
| [Quick start](quick-start.md) | Shortest path from clone to running process. |
| [Configuration](configuration.md) | Configure `.env` using the current environment variables. |
| [Running the bot](running-the-bot.md) | Start and stop the CLI process. |
| [Inviting the bot to Discord](inviting-the-bot-to-discord.md) | Create/invite the Discord bot and enable Message Content Intent. |
| [Interaction paths](interaction-paths.md) | Compare and enable prefix, slash, mention, and DM paths. |
| [Using prefix commands](using-prefix-commands.md) | Send commands such as `!bot ping`. |
| [Built-in commands](built-in-commands.md) | Expected behavior for built-in commands. |
| [Troubleshooting](troubleshooting.md) | Diagnose common first-run issues. |
| [FAQ](faq.md) | Quick answers to common questions. |

Current behavior: the bot runs only as a CLI process through `../../bin/bot.php` or `composer bot`. It can provide slash commands when enabled, but it does not provide a web interface.
