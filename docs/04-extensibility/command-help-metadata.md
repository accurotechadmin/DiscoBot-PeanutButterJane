# Command Help Metadata

**Audience:** Command authors keeping help output useful.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../src/Commands/HelpCommand.php`, `../../src/CommandRouter.php`, `../../src/Commands/CommandInterface.php`, `../../src/Commands/CommandUsage.php`, `../../tests/BuiltInCommandsTest.php`
**Related docs:** [Command interface contract](command-interface-contract.md), [Built-in commands](../01-user-guides/built-in-commands.md), [Example hello command](../05-examples/example-hello-command.md)

Help output is generated from command metadata injected by `CommandRouter` into context config as `commands.metadata`. `HelpCommand` reads that metadata and formats usage, description, and aliases.

## Good metadata

| Method | Good pattern | Avoid |
| --- | --- | --- |
| `description()` | Short present-tense summary, no period required. | Long implementation details or secrets. |
| `usage($prefix)` | A full user-facing example with the active path marker, usually via `CommandUsage::format()`. | Hard-coded `!bot` when `$prefix` is available. |
| Aliases in config | Short alternate names users will remember. | Duplicate command names or hidden behavior. |

## Example

```php
public function description(): string
{
    return 'Greet a Discord user by name.';
}

public function usage(string $prefix): string
{
    return CommandUsage::format($prefix, 'hello <name>');
}
```

## Maintenance checklist

- Add a direct command test for `description()`/`usage()` if the text is important.
- Route `!bot help`, `/help`, mention help, or DM `help` in a router test if path-specific metadata or aliases change.
- Update [Command index](../07-reference/command-index.md) and [Built-in commands](../01-user-guides/built-in-commands.md) only for commands actually registered in `../../config/commands.php`.
