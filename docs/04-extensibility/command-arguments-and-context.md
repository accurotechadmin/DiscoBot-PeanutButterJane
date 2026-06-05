# Command Arguments and Context

**Audience:** Command authors reading input from enabled interaction paths.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../src/CommandContext.php`, `../../src/CommandRouter.php`, `../../src/Commands/EchoCommand.php`, `../../tests/CommandRouterTest.php`
**Related docs:** [Command context reference](../03-technical-reference/command-context-reference.md), [Example argument command](../05-examples/example-argument-command.md), [Safe DiscordPHP object usage](safe-discordphp-object-usage.md)

The router splits message-based arguments on whitespace after the command name. Slash `echo` uses one optional `arguments` string that is split the same way. Quoted strings are not preserved as a single argument in current behavior.

## Argument behavior

With default prefix `!bot`:

| Message | Command | Arguments |
| --- | --- | --- |
| `!bot echo hello world` | `echo` | `['hello', 'world']` |
| `!bot echo   hello\tworld` | `echo` | `['hello', 'world']` |
| `!bot echo` | `echo` | `[]` |

## Snippet: reading arguments

Paste this method body inside a command class that implements `CommandInterface`; use [Example: Argument Command](../05-examples/example-argument-command.md) for a full file.

```php
public function execute(CommandContext $context): string
{
    $arguments = $context->arguments();

    if ($arguments === []) {
        return sprintf('Usage: `%s`', CommandUsage::format($context->prefix(), 'greet <name>'));
    }

    return sprintf('Hello, %s!', implode(' ', $arguments));
}
```

## Context values to prefer

- Use `$context->prefix()` with `CommandUsage::format()` when constructing examples so prefix, slash, mention, and DM usage remain correct.
- Use `$context->config()` for safe configuration values such as timezone or environment.
- Use `$context->commandName()` only when you need the normalized command that actually executed.
- Use `$context->hasMessage()` or `$context->message() !== null` before reading Discord message details.
