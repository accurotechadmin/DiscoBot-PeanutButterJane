# Error Handling

**Audience:** Maintainers and technical readers.
**Status:** Current reference
**Last reviewed:** 2026-06-04
**Related files:** `../../bin/bot.php`, `../../src/Bot.php`, `../../src/CommandRouter.php`, `../../src/ConfigValidator.php`
**Related docs:** [Troubleshooting](../01-user-guides/troubleshooting.md), [Logging reference](logging-reference.md)

## Startup errors

`../../bin/bot.php` catches top-level `Throwable`, writes a timestamped `ERROR` line to STDERR, and exits with status 1. Missing Composer dependencies are handled before autoloading with a direct STDERR message.

## Validation errors

`../../src/ConfigValidator.php` throws `RuntimeException` for invalid bot settings, logging settings, interaction toggles, rate-limit settings, command registry entries, aliases, and slash option metadata.

## Command errors

`../../src/CommandRouter.php` catches command exceptions, logs command name, exception message, file, and line at warning level, then returns a safe generic Discord reply.

## Reply-send errors

`../../src/Bot.php` catches exceptions from public/DM `channel->sendMessage()` and slash `respondWithMessage()`, then logs warnings. `../../src/SlashCommandSynchronizer.php` catches slash synchronization failures per command and logs warnings. The runtime does not retry failed replies.

Current behavior: detailed exception text is not sent to Discord users.
