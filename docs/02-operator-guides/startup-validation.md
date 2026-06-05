# Startup Validation

**Audience:** Operators and maintainers diagnosing startup failures.
**Status:** Current guide
**Last reviewed:** 2026-06-05
**Related files:** `../../bin/bot.php`, `../../bin/sync-slash-commands.php`, `../../config/bot.php`, `../../config/commands.php`, `../../src/ConfigValidator.php`, `../../tests/ConfigValidatorTest.php`
**Related docs:** [Configuration reference](../03-technical-reference/configuration-reference.md), [Environment variable index](../07-reference/environment-variable-index.md)

Startup validation runs before DiscordPHP connects. Both `php bin/bot.php` and `composer sync-slash-commands` validate bot settings and the command registry.

Validation rejects:

- missing, blank, whitespace/control-containing, or non-token-like `DISCORD_BOT_TOKEN`;
- blank, whitespace-containing, control-character, or overlong `BOT_PREFIX`;
- invalid `BOT_TIMEZONE`;
- unsupported `APP_ENV`;
- unsupported `LOG_LEVEL`;
- invalid logging settings;
- invalid interaction toggles or a config where all interaction paths are disabled;
- invalid rate-limit settings, including non-integer or out-of-range values;
- invalid command names, aliases, duplicate command/alias definitions, invalid command classes, invalid slash option names/descriptions/types, and duplicate slash options.

If validation fails, the process writes a clear STDERR error and exits before connecting to Discord.
