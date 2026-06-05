# Installation

**Audience:** Users preparing a local checkout.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../composer.json`, `../../README.md`
**Related docs:** [From GitHub to a running bot](from-github-to-running-bot.md), [Quick start](quick-start.md), [Configuration](configuration.md), [Dependency management](../02-operator-guides/dependency-management.md)

For the complete path from dependency installation through Discord invite, live command checks, and first extension workflow, see [From GitHub to a Running Bot](from-github-to-running-bot.md).

## Requirements

| Requirement | Why it is needed |
| --- | --- |
| PHP 8.1.2 or newer | Declared in `../../composer.json`. |
| Composer | Installs DiscordPHP and PHPUnit. |
| Discord application and bot token | Required to connect to Discord. |
| Message Content Intent | Required for prefix commands that read message text. |

## Install dependencies

```bash
composer install
```

This installs `team-reflex/discord-php` and development tooling declared in `../../composer.json`.

## Fresh-project package equivalents

If you are recreating the skeleton manually instead of cloning it:

```bash
composer require team-reflex/discord-php
composer require --dev phpunit/phpunit
```

## Common pitfalls

- Running `php bin/bot.php` before `composer install` causes the bootstrap to stop with a missing autoload message from `../../bin/bot.php`.
- Composer must be run from the repository root so `vendor/autoload.php` is created where the bootstrap expects it.
- Installing dependencies does not create a Discord bot token; that is handled in the Discord Developer Portal.
