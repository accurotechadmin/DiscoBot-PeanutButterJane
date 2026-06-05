# Example: Command Using Config

**Audience:** Command authors reading safe runtime settings.
**Status:** Current example
**Last reviewed:** 2026-06-03
**Related files:** `../../src/CommandContext.php`, `../../src/Commands/SettingsCommand.php`, `../../src/Commands/TimeCommand.php`, `../../config/bot.php`
**Related docs:** [Command arguments and context](../04-extensibility/command-arguments-and-context.md), [Configuration reference](../03-technical-reference/configuration-reference.md), [Examples index](README.md)

This is an `execute()` snippet. Paste it inside a command class that implements `CommandInterface`; it is not a complete PHP file by itself.

```php
public function execute(CommandContext $context): string
{
    $botConfig = $context->config()['bot'] ?? [];
    $timezone = is_array($botConfig) && isset($botConfig['timezone'])
        ? (string) $botConfig['timezone']
        : 'UTC';

    return sprintf('Configured timezone: `%s`', $timezone);
}
```

## Notes

- Read only safe values for Discord replies. Never print `DISCORD_BOT_TOKEN`.
- Use defaults for missing config keys so direct tests remain simple.
- If a new command requires a new environment variable, update config, validation, tests, and docs together.
