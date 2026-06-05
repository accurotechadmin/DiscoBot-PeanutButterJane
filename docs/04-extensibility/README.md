# Extensibility Guides

**Audience:** Developers adding or maintaining reusable bot commands.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../src/Commands/CommandInterface.php`, `../../src/CommandContext.php`, `../../src/CommandRouter.php`, `../../config/commands.php`, `../../tests/`
**Related docs:** [Adding a command](adding-a-command.md), [Command interface contract](command-interface-contract.md), [Examples](../05-examples/README.md), [Testing new commands](testing-new-commands.md)

Commands are the intended extension point for this skeleton. A command is a small class that implements `App\Commands\CommandInterface`, receives `App\CommandContext`, and returns a string reply.

## Guide map

| Page | Use it for |
| --- | --- |
| [Adding a command](adding-a-command.md) | End-to-end command creation and registration. |
| [Command interface contract](command-interface-contract.md) | Required methods and return values. |
| [Command arguments and context](command-arguments-and-context.md) | Reading arguments, prefix, config, and nullable Discord objects. |
| [Command registration and aliases](command-registration-and-aliases.md) | Updating `config/commands.php` safely. |
| [Command help metadata](command-help-metadata.md) | Keeping help output useful. |
| [Safe DiscordPHP object usage](safe-discordphp-object-usage.md) | Guarding `discord()` and `message()` before use. |
| [Testing new commands](testing-new-commands.md) | Direct command tests and router tests. |
| [Extension patterns](extension-patterns.md) | Patterns that fit the lightweight skeleton. |

## Important boundary

Examples under [05 Examples](../05-examples/README.md) are documentation examples only. They do not become active commands until you add a class under `../../src/Commands/` and register it in `../../config/commands.php`.
