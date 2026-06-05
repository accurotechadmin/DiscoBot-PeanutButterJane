# Safe DiscordPHP Object Usage

**Audience:** Command authors needing DiscordPHP details.
**Status:** Current guide
**Last reviewed:** 2026-06-03
**Related files:** `../../src/CommandContext.php`, `../../src/CommandRouter.php`, `../../src/Bot.php`, `../../tests/CommandRouterTest.php`
**Related docs:** [Command context reference](../03-technical-reference/command-context-reference.md), [Example command with Discord message](../05-examples/example-command-with-discord-message.md), [ADR 0003](../08-architecture-decisions/adr-0003-nullable-discord-objects-for-testability.md)

`CommandContext::discord()` and `CommandContext::message()` are nullable by design. Production routing supplies live objects; direct tests and `routeContent()` often do not.

## Safe pattern

Paste this method body inside a command class that implements `CommandInterface`; use [Example: Command Using Discord Message Safely](../05-examples/example-command-with-discord-message.md) for the matching example context.

```php
public function execute(CommandContext $context): string
{
    $message = $context->message();

    if ($message === null) {
        return 'Message details are unavailable in this context.';
    }

    $authorName = $message->author->username ?? 'unknown user';

    return sprintf('This command was sent by %s.', $authorName);
}
```

## Guidelines

- Prefer returning string replies instead of sending messages directly from commands.
- Use `hasDiscord()` and `hasMessage()` when it makes code easier to read.
- Keep a useful fallback for offline tests.
- Avoid coupling tests to deep DiscordPHP object shapes unless the command truly depends on them.
- If a command needs permissions, channel state, or guild state, document and test the null-object fallback separately.
