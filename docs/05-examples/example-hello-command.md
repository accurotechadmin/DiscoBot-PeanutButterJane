# Example: Hello Command

**Audience:** Command authors creating a first command.
**Status:** Current example
**Last reviewed:** 2026-06-04
**Related files:** `../../src/Commands/CommandInterface.php`, `../../src/CommandContext.php`, `../../config/commands.php`
**Related docs:** [Adding a command](../04-extensibility/adding-a-command.md), [Command interface contract](../04-extensibility/command-interface-contract.md), [Examples index](README.md)

This is a full command file example. Save it as `src/Commands/HelloCommand.php`, then register it in `config/commands.php` before expecting Discord users to call it.

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
        return 'Say hello from the bot.';
    }

    public function usage(string $prefix): string
    {
        return CommandUsage::format($prefix, 'hello');
    }
}
```

## Registration snippet

```php
use App\Commands\HelloCommand;

return [
    // Existing commands...
    'hello' => HelloCommand::class,
];
```

## Expected Discord behavior

With the default prefix, `!bot hello` replies:

```text
Hello from the bot!
```
