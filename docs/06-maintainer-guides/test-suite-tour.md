# Test Suite Tour

**Audience:** Maintainers extending or verifying tests.
**Status:** Current guide
**Last reviewed:** 2026-06-05
**Related files:** `../../tests/BuiltInCommandsTest.php`, `../../tests/CommandRouterTest.php`, `../../tests/ConfigValidatorTest.php`, `../../tests/ConsoleLoggerTest.php`, `../../tests/RateLimiterTest.php`, `../../tests/RuntimeLifecycleTest.php`, `../../phpunit.xml`
**Related docs:** [Testing reference](../03-technical-reference/testing-reference.md), [Testing new commands](../04-extensibility/testing-new-commands.md), [Example tests for a new command](../05-examples/example-tests-for-new-command.md)

The suite is intentionally offline. It verifies command and routing behavior without connecting to Discord.

| Test file | Protects |
| --- | --- |
| `../../tests/BuiltInCommandsTest.php` | Built-in command replies, help text shape, `time`, `settings`, and `echo` behavior. |
| `../../tests/CommandRouterTest.php` | Prefix, mention, DM, and slash dispatch paths; aliases, metadata, unknown commands, slash definitions, and safe exception handling. |
| `../../tests/ConfigValidatorTest.php` | Startup validation failures, accepted config, interaction-path toggle validation, and rate-limit integer validation. |
| `../../tests/ConsoleLoggerTest.php` | Log-level filtering, constructor validation, and structured daily JSON file output. |
| `../../tests/RateLimiterTest.php` | Basic in-memory command rate limiting behavior. |
| `../../tests/RuntimeLifecycleTest.php` | Runtime lifecycle state, idempotent shutdown requests, shutdown logger registration, and signal-handler installation contract. |

## Before adding commands

- Add direct tests for command output and usage text.
- Add router tests for aliases or parser-dependent behavior.
- Use `CommandContext` with `discord: null` and `message: null` unless live Discord objects are essential.
- Update docs examples and reference indexes only after registration is real.

## Commands

| Command | Purpose |
| --- | --- |
| `composer test` | Run PHPUnit. |
| `composer check` | Run lint and tests. |
