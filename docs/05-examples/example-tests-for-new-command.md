# Example: Tests for a New Command

**Audience:** Command authors looking for copyable snippets.
**Status:** Current example
**Last reviewed:** 2026-06-03
**Related files:** `../../src/Commands/CommandInterface.php`, `../../src/CommandContext.php`, `../../config/commands.php`, `../../tests/CommandRouterTest.php`
**Related docs:** [Extensibility](../04-extensibility/README.md), [Testing new commands](../04-extensibility/testing-new-commands.md)

Snippet aligned with the current PHPUnit style:

```php
<?php

declare(strict_types=1);

namespace Tests;

use App\CommandContext;
use App\CommandRouter;
use App\Commands\HelloCommand;
use PHPUnit\Framework\TestCase;

final class HelloCommandTest extends TestCase
{
    public function testHelloCommandReplies(): void
    {
        $command = new HelloCommand();
        $context = new CommandContext(null, null, 'hello', [], '!bot', [
            'bot' => ['prefix' => '!bot'],
            'commands' => [],
        ]);

        self::assertSame('Hello from the bot!', $command->execute($context));
    }

    public function testHelloCommandRoutesFromRawContent(): void
    {
        $router = new CommandRouter(['hello' => HelloCommand::class]);

        self::assertSame('Hello from the bot!', $router->routeContent('!bot hello', '!bot', [
            'bot' => ['prefix' => '!bot'],
            'commands' => [],
        ]));
    }
}
```

If you add this test for real, also add `HelloCommand.php` under `../../src/Commands/`.
