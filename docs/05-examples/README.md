# Examples

**Audience:** Command authors who want copyable snippets.
**Status:** Current example set
**Last reviewed:** 2026-06-03
**Related files:** `../../src/Commands/CommandInterface.php`, `../../src/CommandContext.php`, `../../config/commands.php`, `../../tests/BuiltInCommandsTest.php`, `../../tests/CommandRouterTest.php`
**Related docs:** [Adding a command](../04-extensibility/adding-a-command.md), [Command registration and aliases](../04-extensibility/command-registration-and-aliases.md), [Testing new commands](../04-extensibility/testing-new-commands.md)

These examples match the current command interface and registry shape. They are documentation examples only: they do not become active commands until you add a class under `../../src/Commands/` and register it in `../../config/commands.php`.

## Example map

| Example | Shows | Copy status |
| --- | --- | --- |
| [Hello command](example-hello-command.md) | Minimal full command file. | Full file. |
| [Argument command](example-argument-command.md) | Reading `$context->arguments()` and `$context->prefix()`. | Full file. |
| [Alias registration](example-alias-command.md) | Registry array form with aliases. | Snippet. |
| [Command using config](example-command-with-config.md) | Reading safe values from `$context->config()`. | Snippet. |
| [Command using Discord message safely](example-command-with-discord-message.md) | Null-guarding `$context->message()`. | Snippet. |
| [Tests for a new command](example-tests-for-new-command.md) | PHPUnit direct command tests. | Full test-file pattern. |
| [Delivery operations bot blueprint](delivery-operations-bot-blueprint.md) | Future delivery-business command suite, route-note workflow, dispatcher/manager tools, and MVP planning boundaries. | Planning reference; all unimplemented product ideas are labeled **Future consideration**. |
| [Delivery operations data requirements index](delivery-operations-data-requirements-index.md) | Data needed to calculate proposed delivery outputs such as stops/hour, required pace, route notes, risk, rescues, broadcasts, and manager summaries. | Planning reference; all unimplemented data and calculations are labeled **Future consideration**. |

## Verification checklist for examples

- Include `declare(strict_types=1);` in full PHP files.
- Use `namespace App\Commands;` for command classes.
- Import `App\CommandContext`.
- Implement `CommandInterface`.
- Return string replies from `execute()`.
- Use current registry shape from `../../config/commands.php`.
