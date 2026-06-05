# Testing Reference

**Audience:** Maintainers and technical readers.
**Status:** Current reference
**Last reviewed:** 2026-06-05
**Related files:** `../../phpunit.xml`, `../../composer.json`, `../../tests/BuiltInCommandsTest.php`, `../../tests/CommandRouterTest.php`, `../../tests/ConfigValidatorTest.php`, `../../tests/ConsoleLoggerTest.php`, `../../tests/RateLimiterTest.php`, `../../tests/RuntimeLifecycleTest.php`
**Related docs:** [Test suite tour](../06-maintainer-guides/test-suite-tour.md), [Example tests for a new command](../05-examples/example-tests-for-new-command.md)

The test suite uses PHPUnit and avoids live Discord network calls.

## Test files

| File | Coverage |
| --- | --- |
| `../../tests/BuiltInCommandsTest.php` | Direct command output shapes. |
| `../../tests/CommandRouterTest.php` | Prefix, mention, DM, and slash dispatch behavior plus aliases, metadata, errors, and registry behavior. |
| `../../tests/ConfigValidatorTest.php` | Startup validation rules, including interaction-path toggles and integer rate-limit settings. |
| `../../tests/ConsoleLoggerTest.php` | Logger filtering, invalid levels, and structured daily JSON file output. |
| `../../tests/RateLimiterTest.php` | Basic in-memory command rate limiting behavior. |
| `../../tests/RuntimeLifecycleTest.php` | Runtime lifecycle state, idempotent shutdown requests, shutdown logger registration, and signal-handler installation contract. |

## Useful commands

```bash
composer test
composer check
```

These commands require development dependencies from `composer install`; if `vendor/bin/phpunit` is missing, install dependencies before rerunning `composer test` or `composer check`.

Command tests can instantiate `CommandContext` with `null` Discord objects or call `CommandRouter::routeContent()` to test routing without DiscordPHP messages.
