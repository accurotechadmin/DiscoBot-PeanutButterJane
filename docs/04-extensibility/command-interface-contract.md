# Command Interface Contract

**Audience:** Command authors implementing new commands.
**Status:** Current reference
**Last reviewed:** 2026-06-04
**Related files:** `../../src/Commands/CommandInterface.php`, `../../src/Commands/CommandUsage.php`, `../../src/CommandContext.php`, `../../src/Commands/HelpCommand.php`
**Related docs:** [Adding a command](adding-a-command.md), [Command help metadata](command-help-metadata.md), [Command context reference](../03-technical-reference/command-context-reference.md)

Every command must implement `App\Commands\CommandInterface`.

```php
public function execute(CommandContext $context): string;
public function description(): string;
public function usage(string $prefix): string;
```

## Method responsibilities

| Method | Required behavior | Used by |
| --- | --- | --- |
| `execute(CommandContext $context): string` | Perform command logic and return the Discord reply text. Return an empty string only when the bot should not send a reply. | `../../src/CommandRouter.php`, then `../../src/Bot.php` sends non-empty replies. |
| `description(): string` | Provide a short human-readable summary. | Router metadata and `HelpCommand`. |
| `usage(string $prefix): string` | Provide a full usage example for the active interaction path. Use `CommandUsage::format()` so prefix, slash, mention, and DM usage are formatted correctly. | Router metadata and `HelpCommand`. |

## Style expectations

- Use `declare(strict_types=1);`.
- Put command classes under `namespace App\Commands;`.
- Import `App\CommandContext`.
- Return string replies; do not send Discord messages directly unless the command explicitly needs special Discord behavior.
- Use `$context->arguments()` for user input and `$context->prefix()` with `CommandUsage::format()` for usage hints.
- Guard `$context->discord()` and `$context->message()` against `null` before using DiscordPHP APIs.
