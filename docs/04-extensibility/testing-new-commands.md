# Testing New Commands

**Audience:** Developers adding command tests.
**Status:** Current guide
**Last reviewed:** 2026-06-03
**Related files:** `../../tests/BuiltInCommandsTest.php`, `../../tests/CommandRouterTest.php`, `../../src/CommandContext.php`, `../../src/CommandRouter.php`
**Related docs:** [Testing reference](../03-technical-reference/testing-reference.md), [Example tests for a new command](../05-examples/example-tests-for-new-command.md), [Command arguments and context](command-arguments-and-context.md)

The current test suite avoids Discord network calls. New command tests should follow that pattern unless a future feature deliberately adds integration testing.

## Recommended test layers

| Layer | What it checks | Example source |
| --- | --- | --- |
| Direct command test | `execute()`, `description()`, and `usage()` behavior with a constructed `CommandContext`. | `../../tests/BuiltInCommandsTest.php` |
| Router raw-content test | Prefix parsing, aliases, arguments, metadata injection, and safe exception handling. | `../../tests/CommandRouterTest.php` |
| Config validation test | New config rules if a command introduces documented config values. | `../../tests/ConfigValidatorTest.php` |

## Direct command context

```php
$context = new CommandContext(
    discord: null,
    message: null,
    commandName: 'hello',
    arguments: ['Ada'],
    prefix: '!bot',
    config: ['bot' => ['timezone' => 'UTC', 'env' => 'testing']],
);
```

## Checklist

- Test empty arguments if the command accepts input.
- Test custom prefixes when usage text includes `$context->prefix()`.
- Test nullable Discord object fallbacks when the command reads `message()` or `discord()`.
- Run `composer test` or `composer check` before committing code changes.
