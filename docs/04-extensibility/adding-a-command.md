# Adding a Command

**Audience:** Command authors adding reusable bot commands.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../src/Commands/CommandInterface.php`, `../../src/CommandContext.php`, `../../config/commands.php`, `../../tests/CommandRouterTest.php`
**Related docs:** [Hello command example](../05-examples/example-hello-command.md), [Command registration and aliases](command-registration-and-aliases.md), [Testing new commands](testing-new-commands.md)

## Steps

1. Create a class under `../../src/Commands/`.
2. Declare `strict_types=1` and `namespace App\Commands;`.
3. Implement `CommandInterface`.
4. Import `App\CommandContext`.
5. Return a string from `execute()`.
6. Provide `description()` and `usage()` for help output.
7. Register the class in `../../config/commands.php`.
8. If the command needs slash arguments, add `slash_options` metadata in `../../config/commands.php`.
9. Add or update tests.

## Full file example

```php
<?php

declare(strict_types=1);

namespace App\Commands;

use App\CommandContext;

final class HelloCommand implements CommandInterface
{
    public function execute(CommandContext $context): string
    {
        return 'Hello from the bot!';
    }

    public function description(): string
    {
        return 'Say hello.';
    }

    public function usage(string $prefix): string
    {
        return CommandUsage::format($prefix, 'hello');
    }
}
```

Then register it:

```php
use App\Commands\HelloCommand;

return [
    // existing commands...
    'hello' => HelloCommand::class,
];
```

Documentation examples are not active commands until both files are changed.

## Optional slash argument metadata

When slash commands are enabled, every registered command and alias can be registered as a slash command. If a new command needs arguments through Discord's slash-command UI, wrap the registry entry in an array and add `slash_options`. This keeps the command interface unchanged; Discord option values are passed to `CommandContext::arguments()` just like parsed message arguments. See [Command registration and aliases](command-registration-and-aliases.md) for the registry shape.
