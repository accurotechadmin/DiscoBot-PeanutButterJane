# Inviting the Bot to Discord

**Audience:** Users creating and inviting a Discord bot application.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../.env.example`, `../../src/Bot.php`
**Related docs:** [From GitHub to a running bot](from-github-to-running-bot.md), [Quick start](quick-start.md), [Troubleshooting](troubleshooting.md), [DiscordPHP integration](../03-technical-reference/discordphp-integration.md)

## Invite checklist

1. Open the Discord Developer Portal.
2. Create or select an application.
3. Add a bot user under the application settings.
4. Copy the bot token into `.env` as `DISCORD_BOT_TOKEN`.
5. Enable **Message Content Intent** under the bot settings.
6. Use OAuth2 URL generation with the `bot` scope. Add the `applications.commands` scope too if slash commands will be enabled.
7. Select permissions that allow the bot to view channels and send messages.
8. Open the generated invite URL and add the bot to your server.

## Permission basics

For prefix and mention commands, the bot needs access to the channel where users type commands and permission to send replies. If help output appears missing in one channel but works in another, check Discord channel permissions first.

## Why Message Content Intent matters

Prefix, mention, and DM commands depend on reading message text such as `!bot ping`, `@YourBot ping`, or `ping` in a DM. `../../src/Bot.php` requests DiscordPHP's message content intent when message-based paths are enabled, but the same privileged intent must also be enabled in the Developer Portal.

## Interaction-path note

Current behavior: this skeleton enables prefix commands by default and can optionally register slash commands when `BOT_ENABLE_SLASH_COMMANDS=true`. Slash replies are ephemeral; prefix and mention replies are public server messages; DM replies are private one-to-one messages.
