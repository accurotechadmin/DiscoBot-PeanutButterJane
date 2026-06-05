# Example: Argument Command

**Audience:** Command authors looking for copyable snippets.
**Status:** Current example
**Last reviewed:** 2026-06-04
**Related files:** `../../src/Commands/CommandInterface.php`, `../../src/CommandContext.php`, `../../config/commands.php`, `../../tests/CommandRouterTest.php`
**Related docs:** [Extensibility](../04-extensibility/README.md), [Testing new commands](../04-extensibility/testing-new-commands.md)

Full file example. Save it as `src/Commands/GreetCommand.php`, then register it in `config/commands.php` before expecting Discord users to call it:

```php
<?php

declare(strict_types=1);

namespace App\Commands;

use App\CommandContext;

final class GreetCommand implements CommandInterface
{
    public function execute(CommandContext $context): string
    {
        $name = trim(implode(' ', $context->arguments()));

        if ($name === '') {
            return sprintf('Usage: `%s`', CommandUsage::format($context->prefix(), 'greet <name>'));
        }

        return sprintf('Hello, %s!', $name);
    }

    public function description(): string
    {
        return 'Greet a named person.';
    }

    public function usage(string $prefix): string
    {
        return CommandUsage::format($prefix, 'greet <name>');
    }
}
```


## Registration snippet

```php
use App\Commands\GreetCommand;

return [
    // Existing commands...
    'greet' => GreetCommand::class,
];
```

With the default prefix, `!bot greet Ada Lovelace` replies `Hello, Ada Lovelace!`.
