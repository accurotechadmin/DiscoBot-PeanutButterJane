# Example: Command Using Discord Message Safely

**Audience:** Command authors who need DiscordPHP message details.
**Status:** Current example
**Last reviewed:** 2026-06-03
**Related files:** `../../src/CommandContext.php`, `../../src/CommandRouter.php`, `../../src/Bot.php`, `../../tests/CommandRouterTest.php`
**Related docs:** [Safe DiscordPHP object usage](../04-extensibility/safe-discordphp-object-usage.md), [Command context reference](../03-technical-reference/command-context-reference.md), [Examples index](README.md)

This is an `execute()` snippet. Paste it inside a command class that implements `CommandInterface`; it is not a complete PHP file by itself. It demonstrates the null guard required because `CommandContext::message()` is nullable.

```php
public function execute(CommandContext $context): string
{
    $message = $context->message();

    if ($message === null) {
        return 'Message details are unavailable in this context.';
    }

    $username = $message->author->username ?? 'unknown user';

    return sprintf('Command received from %s.', $username);
}
```

## Why the guard matters

Production routing supplies the live DiscordPHP message. Unit tests and `CommandRouter::routeContent()` can pass `null` so command logic can be tested without Discord network calls or DiscordPHP fakes.
