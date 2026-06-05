# Quick Start

**Audience:** Users who want the shortest working path.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../.env.example`, `../../bin/bot.php`, `../../config/bot.php`, `../../config/commands.php`
**Related docs:** [From GitHub to a running bot](from-github-to-running-bot.md), [Installation](installation.md), [Inviting the bot to Discord](inviting-the-bot-to-discord.md), [Running the bot](running-the-bot.md), [Built-in commands](built-in-commands.md)

For a fuller guide that includes readiness checks, Discord-side setup, troubleshooting, and the first extension workflow, see [From GitHub to a Running Bot](from-github-to-running-bot.md).

## Steps

1. Install dependencies:

   ```bash
   composer install
   ```

2. Create your local environment file:

   ```bash
   cp .env.example .env
   ```

3. Edit `.env` and set `DISCORD_BOT_TOKEN` to your bot token. Keep `BOT_PREFIX=!bot` unless you want a different prefix.

4. In the Discord Developer Portal, enable **Message Content Intent** for the bot application.

5. Invite the bot to a server with permissions to view channels, read message history, and send messages.

6. Start the process:

   ```bash
   php bin/bot.php
   ```

   or:

   ```bash
   composer bot
   ```

7. In Discord, try:

   ```text
   !bot ping
   !bot help
   ```

## Expected result

`!bot ping` replies `Pong!`. `!bot help` lists the registered commands from `../../config/commands.php` using metadata supplied by each command class.

## If it does not work

Use [Troubleshooting](troubleshooting.md), especially the checks for missing token, disabled Message Content Intent, wrong prefix, and missing Composer dependencies.
