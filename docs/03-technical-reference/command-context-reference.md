# Command Context Reference

**Audience:** Command authors and maintainers.
**Status:** Current reference
**Last reviewed:** 2026-06-04
**Related files:** `../../src/CommandContext.php`, `../../src/CommandRouter.php`, `../../tests/BuiltInCommandsTest.php`, `../../tests/CommandRouterTest.php`
**Related docs:** [Command arguments and context](../04-extensibility/command-arguments-and-context.md), [Safe DiscordPHP object usage](../04-extensibility/safe-discordphp-object-usage.md), [ADR 0003](../08-architecture-decisions/adr-0003-nullable-discord-objects-for-testability.md)

`App\CommandContext` is the value object passed to every command's `execute()` method. It keeps command classes independent from the router while still exposing the data needed for replies.

## Constructor data

| Value | Type | Production source | Test/offline behavior |
| --- | --- | --- | --- |
| Discord client | `?Discord\Discord` | Supplied by `CommandRouter::route()` from `Bot`. | May be `null` in direct tests and `routeContent()`. |
| Message | `?Discord\Parts\Channel\Message` | Supplied by `CommandRouter::route()`. | May be `null` in direct tests and `routeContent()`. |
| Command name | `string` | Normalized command or alias target. | Caller/test supplied. |
| Arguments | `list<string>` | Whitespace-split parser output. | Caller/test supplied. |
| Usage prefix | `string` | Active invocation marker such as `!bot`, `/`, `<@bot-id>`, or an empty DM prefix. | Caller/test supplied. |
| Config | `array<string,mixed>` | Runtime config plus injected command metadata. | Caller/test supplied. |

## Accessors

| Method | Returns | Use |
| --- | --- | --- |
| `discord()` | `?Discord` | Access the live client only after a null check. |
| `message()` | `?Message` | Access the original message only after a null check. |
| `hasDiscord()` | `bool` | Readable guard before client-specific behavior. |
| `hasMessage()` | `bool` | Readable guard before message-specific behavior. |
| `commandName()` | `string` | Normalized command name actually executed. |
| `arguments()` | `list<string>` | Command arguments split on whitespace. |
| `prefix()` | `string` | Build usage hints matching the active interaction path. |
| `config()` | `array<string,mixed>` | Read bot settings and command metadata. |

## Design note

Nullable DiscordPHP objects are intentional. They let tests exercise command logic without network calls or heavy DiscordPHP fakes. Commands that need Discord-specific state should provide a useful fallback when the objects are absent.
