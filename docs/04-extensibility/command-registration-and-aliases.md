# Command Registration and Aliases

**Audience:** Command authors updating the command registry.
**Status:** Current guide
**Last reviewed:** 2026-06-04
**Related files:** `../../config/commands.php`, `../../src/CommandRouter.php`, `../../src/Commands/HelpCommand.php`, `../../tests/CommandRouterTest.php`
**Related docs:** [Adding a command](adding-a-command.md), [Command routing reference](../03-technical-reference/command-routing-reference.md), [Example alias command](../05-examples/example-alias-command.md)

`../../config/commands.php` is the current source of command registration. Command names are the words users type after the prefix, after a bot mention, in a DM, or as slash command names when slash commands are enabled.

## Simple registration

```php
use App\Commands\HelloCommand;

return [
    'hello' => HelloCommand::class,
];
```

The router instantiates the class and verifies it implements `CommandInterface`.

## Registration with aliases

```php
use App\Commands\HelpCommand;

return [
    'help' => [
        'class' => HelpCommand::class,
        'aliases' => ['commands'],
    ],
];
```

Aliases resolve before command lookup. They are shown as aliases in help metadata, not duplicated as standalone help entries. When slash commands are enabled, aliases are also registered as slash command names.

## Registration with slash options

Slash commands are registered from the same command registry when `BOT_ENABLE_SLASH_COMMANDS=true`. Commands that need slash arguments can declare `slash_options` without changing `CommandInterface`:

```php
use App\Commands\EchoCommand;

return [
    'echo' => [
        'class' => EchoCommand::class,
        'slash_options' => [
            [
                'name' => 'arguments',
                'description' => 'Optional command arguments as text.',
                'type' => 3,
                'required' => false,
            ],
        ],
    ],
];
```

`type` is the Discord application-command option type integer; `3` is a string option. If `type` is omitted, `Bot` registers the option as a string. Option values are collected into `CommandContext::arguments()` after Discord invokes the slash listener. Aliases inherit the canonical command's slash options.

## Checklist

- Use lowercase command and alias names for readability; the router normalizes names anyway. If slash commands are enabled, keep names compatible with Discord slash command naming rules.
- Avoid aliases that are empty or identical to the canonical command name.
- Add or update router tests when changing parser, alias, slash-definition, slash-option, or metadata behavior.
- Update [Command index](../07-reference/command-index.md), user command docs, and examples when registering a real command.
