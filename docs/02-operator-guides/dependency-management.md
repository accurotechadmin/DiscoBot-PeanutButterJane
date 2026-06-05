# Dependency Management

**Audience:** Operators and maintainers updating dependencies.
**Status:** Current guide
**Last reviewed:** 2026-06-03
**Related files:** `../../composer.json`, `../../phpunit.xml`
**Related docs:** [Installation](../01-user-guides/installation.md), [Composer scripts reference](../03-technical-reference/composer-scripts-reference.md), [Testing reference](../03-technical-reference/testing-reference.md), [Release readiness checklist](../06-maintainer-guides/release-readiness-checklist.md)

Composer is the dependency manager. `../../composer.json` requires PHP 8.1.2+ and `team-reflex/discord-php`, with PHPUnit available for development tests.

## Routine commands

| Command | Purpose | When to run |
| --- | --- | --- |
| `composer install` | Install dependencies declared by the project. | New checkout, deployment, CI setup. |
| `composer update` | Refresh dependency versions when intentionally upgrading. | Planned dependency maintenance only. |
| `composer lint` | Syntax-check PHP files in `bin`, `src`, `config`, and `tests`. | Before commits that touch PHP. |
| `composer test` | Run PHPUnit tests. | Before commits and after command/router/config changes. |
| `composer check` | Run lint and tests. | Preferred final local verification. |

## Operational guidance

- Review DiscordPHP release notes before major upgrades because `../../src/Bot.php` constructs the Discord client directly.
- If Composer dependencies are unavailable, `../../bin/bot.php` exits before reading config and tells the operator to run `composer install`.
- Keep documentation synchronized if scripts, package requirements, supported PHP versions, or test commands change.
- Do not document a deployment-only package or platform requirement unless it is added to source control and verified.

## Dependency-change checklist

1. Update dependencies intentionally, not as part of unrelated documentation work.
2. Run `composer check`.
3. Re-read affected source paths, especially DiscordPHP integration and tests.
4. Update [Installation](../01-user-guides/installation.md), [Composer scripts reference](../03-technical-reference/composer-scripts-reference.md), and release notes/checklists if behavior changes.
