# Composer Scripts Reference

**Audience:** Developers, operators, and maintainers running project commands.
**Status:** Current reference
**Last reviewed:** 2026-06-03
**Related files:** `../../composer.json`, `../../bin/bot.php`, `../../bin/sync-slash-commands.php`, `../../phpunit.xml`
**Related docs:** [Installation](../01-user-guides/installation.md), [Dependency management](../02-operator-guides/dependency-management.md), [Testing reference](testing-reference.md)

`../../composer.json` defines the project scripts. These are convenience wrappers around PHP commands already present in the repository.

| Script | Command | Purpose |
| --- | --- | --- |
| `composer bot` | `php bin/bot.php` | Start the long-running Discord bot process. |
| `composer sync-slash-commands` | `php bin/sync-slash-commands.php` | Connect briefly and synchronize slash command definitions outside normal runtime startup. |
| `composer lint` | `find bin src config tests -name '*.php' -print0 \| xargs -0 -n1 php -l` | Syntax-check application and test PHP files. |
| `composer test` | `vendor/bin/phpunit` | Run the PHPUnit suite configured by `../../phpunit.xml`. |
| `composer check` | `@lint`, then `@test` | Run syntax checks and tests in sequence. |

## Notes

- `composer bot` requires a valid runtime environment and Discord credentials.
- `composer sync-slash-commands` requires Discord credentials and should be rerun after command registry, alias, or slash option changes.
- `composer test` does not connect to Discord; tests use nullable context objects and raw-content routing.
- If `vendor/` is missing, run `composer install` before these scripts.
- Update this page if script names, command scopes, or test tooling change.
