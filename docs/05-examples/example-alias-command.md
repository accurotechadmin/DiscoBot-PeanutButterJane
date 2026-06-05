# Example: Alias Registration

**Audience:** Command authors adding alternate command names.
**Status:** Current example
**Last reviewed:** 2026-06-03
**Related files:** `../../config/commands.php`, `../../src/CommandRouter.php`, `../../tests/CommandRouterTest.php`
**Related docs:** [Command registration and aliases](../04-extensibility/command-registration-and-aliases.md), [Command routing reference](../03-technical-reference/command-routing-reference.md), [Examples index](README.md)

This is a registry snippet, not a full file. It matches the array form currently used by the built-in `help` command.

```php
use App\Commands\HelpCommand;

return [
    'help' => [
        'class' => HelpCommand::class,
        'aliases' => ['commands'],
    ],
];
```

## Behavior

- A user typing `!bot commands` routes to the `help` command.
- The canonical command name in `CommandContext` is `help` after alias resolution.
- Help metadata lists `commands` as an alias rather than duplicating a separate command row.

## When adding your own alias

- Register the alias in `../../config/commands.php`.
- Add or update a router test if alias behavior changes.
- Update user-facing docs only when the alias is part of the supported command surface.
